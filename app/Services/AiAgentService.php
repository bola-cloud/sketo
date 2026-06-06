<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiAgentService
{
    protected $apiUrl;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiUrl = env('AI_API_URL', 'http://127.0.0.1:7777/v1/chat/completions');
        $this->apiKey = env('AI_API_KEY', 'change-secret-key-2026');
        $this->model = env('AI_MODEL', 'gpt-4o-mini');
    }

    /**
     * Define the tools available to the AI via Schema.
     */
    protected function getTools(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_sales_overview',
                    'description' => 'Get a sales summary for a specific number of days (e.g., last 7 days, last 30 days). Use this when the user asks for sales "before today" or "last week".',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'days' => ['type' => 'integer', 'description' => 'Number of days to look back (default 1 for today)']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_categories',
                    'description' => 'Get a list of product categories to help assign the right category_id when creating a product.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'create_product',
                    'description' => 'Create a new product in the database. Use this when the user asks to add a new product.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string', 'description' => 'Product name'],
                            'category_id' => ['type' => 'integer', 'description' => 'The ID of the category'],
                            'cost_price' => ['type' => 'number', 'description' => 'Purchase price'],
                            'selling_price' => ['type' => 'number', 'description' => 'Retail price'],
                            'quantity' => ['type' => 'integer', 'description' => 'Initial stock quantity'],
                            'barcode' => ['type' => 'string', 'description' => 'Barcode (optional)']
                        ],
                        'required' => ['name', 'category_id', 'selling_price', 'quantity']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_today_summary',
                    'description' => 'Get a quick financial summary for ONLY today.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_low_stock_products',
                    'description' => 'Get a list of products that have reached or dropped below their minimum allowed stock threshold.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_expiring_products',
                    'description' => 'Get a list of products that are about to expire within their defined expiry alert period.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_recent_clients',
                    'description' => 'Get a list of the 5 most recently added clients.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'set_product_subunit',
                    'description' => 'Link a bulk product (main) to a retail product (sub) with a conversion factor. e.g. 1 Box = 12 Sachets.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'main_product_id' => ['type' => 'integer', 'description' => 'ID of the bulk product'],
                            'sub_product_id' => ['type' => 'integer', 'description' => 'ID of the retail product'],
                            'conversion_factor' => ['type' => 'number', 'description' => 'How many sub-items in one main item']
                        ],
                        'required' => ['main_product_id', 'sub_product_id', 'conversion_factor']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_stagnant_products',
                    'description' => 'Get a list of stagnant products that have had zero sales in the last 30 days.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_purchase_summary',
                    'description' => 'Get a summary of recent purchase invoices (stock incoming).',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_top_selling_products',
                    'description' => 'Identify the top 5 best-selling products by quantity.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_profit_summary',
                    'description' => 'Get a gross profit summary for a specific period (default today).',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'days' => ['type' => 'integer', 'description' => 'Number of days to look back (default 1)']
                        ],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_recent_invoices',
                    'description' => 'Get the 5 most recent sales invoices including client name and paid amount.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_financial_dues',
                    'description' => 'Get a summary of unpaid invoices (debts we owe to suppliers, and credits clients owe us) to help with financial scheduling.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_recent_returns',
                    'description' => 'Get a summary of recent customer returns (products that were returned by clients). Use this when asked about returns.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[]
                    ]
                ]
            ]
        ];
    }

    /**
     * Map tool names to internal functions safely passing tenant separation logic.
     */
    protected function executeTool(string $functionName, array $args = []): string
    {
        // TENANT ISOLATION IS ENFORCED HERE: The AI cannot pass vendor_id.
        $vendorId = auth()->user()->vendor_id;

        try {
            switch ($functionName) {
                case 'get_today_summary':
                case 'get_sales_overview':
                    $days = $args['days'] ?? ($functionName === 'get_today_summary' ? 0 : 7);
                    $startDate = $days == 0 ? Carbon::today() : Carbon::now()->subDays($days)->startOfDay();
                    
                    $totalSold = Sales::where('vendor_id', $vendorId)
                        ->where('created_at', '>=', $startDate)
                        ->sum('quantity');
                        
                    $totalRevenue = Invoice::where('vendor_id', $vendorId)
                        ->where('created_at', '>=', $startDate)
                        ->sum('total_amount');
                        
                    $topSoldThisPeriod = Sales::where('vendor_id', $vendorId)
                        ->where('created_at', '>=', $startDate)
                        ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
                        ->with('product:id,name')
                        ->groupBy('product_id')
                        ->orderByDesc('total_qty')
                        ->limit(3)
                        ->get()
                        ->map(function($sale) {
                            return [
                                'product_name' => $sale->product->name ?? 'غير محدد',
                                'quantity_sold' => (float)$sale->total_qty
                            ];
                        });
                    
                    return json_encode([
                        'status' => 'success', 
                        'period' => $days == 0 ? "اليوم" : "آخر $days أيام",
                        'from_date' => $startDate->toDateString(),
                        'to_date' => Carbon::now()->toDateString(),
                        'total_items_sold' => (float)$totalSold, 
                        'total_revenue' => (float)$totalRevenue,
                        'top_items_sold_this_period' => $topSoldThisPeriod,
                        'currency' => auth()->user()->vendor->currency ?? 'ج.م'
                    ]);

                case 'get_categories':
                    $categories = \App\Models\Category::where('vendor_id', $vendorId)->get(['id', 'name']);
                    return json_encode(['status' => 'success', 'categories' => $categories]);
                    break;

                case 'create_product':
                    $product = \App\Models\Product::create([
                        'vendor_id' => $vendorId,
                        'name' => $args['name'],
                        'category_id' => $args['category_id'],
                        'cost_price' => $args['cost_price'] ?? 0,
                        'selling_price' => $args['selling_price'],
                        'quantity' => $args['quantity'],
                        'barcode' => $args['barcode'] ?? 'AI-'.time(),
                        'threshold' => 5,
                        'color' => '' // Added to satisfy DB constraint
                    ]);
                    return json_encode(['status' => 'success', 'message' => "تم إضافة المنتج ({$product->name}) بنجاح."]);
                    break;

                case 'set_product_subunit':
                    \App\Models\ProductSubUnit::updateOrCreate(
                        ['main_product_id' => $args['main_product_id'], 'sub_product_id' => $args['sub_product_id']],
                        ['vendor_id' => $vendorId, 'conversion_factor' => $args['conversion_factor']]
                    );
                    return json_encode(['status' => 'success', 'message' => 'تم ربط الوحدات بنجاح.']);
                    break;

                case 'get_low_stock_products':
                    $products = Product::where('vendor_id', $vendorId)
                        ->whereColumn('quantity', '<=', 'threshold')
                        ->select('name', 'quantity', 'threshold')
                        ->get();
                    return json_encode(['status' => 'success', 'low_stock_items' => $products]);

                case 'get_top_selling_products':
                    $topProducts = Sales::where('vendor_id', $vendorId)
                        ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
                        ->with('product:id,name')
                        ->groupBy('product_id')
                        ->orderByDesc('total_qty')
                        ->limit(5)
                        ->get();
                    return json_encode(['status' => 'success', 'top_products' => $topProducts]);

                case 'get_purchase_summary':
                    $recentPurchases = \App\Models\Purchase::where('vendor_id', $vendorId)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                    return json_encode(['status' => 'success', 'recent_purchases' => $recentPurchases]);

                case 'get_profit_summary':
                    $days = $args['days'] ?? 0; // 0 means today
                    $startDate = $days == 0 ? Carbon::today() : Carbon::now()->subDays($days)->startOfDay();
                    
                    $sales = Sales::where('vendor_id', $vendorId)
                        ->where('created_at', '>=', $startDate)
                        ->with('product:id,cost_price,selling_price')
                        ->get();
                    
                    $totalCost = 0;
                    $totalRevenue = 0;
                    foreach($sales as $sale) {
                        $totalCost += ($sale->product->cost_price ?? 0) * $sale->quantity;
                        $totalRevenue += $sale->total_price;
                    }
                    
                    return json_encode([
                        'status' => 'success',
                        'period_days' => $days,
                        'revenue' => $totalRevenue,
                        'cost' => $totalCost,
                        'gross_profit' => $totalRevenue - $totalCost,
                        'currency' => auth()->user()->vendor->currency ?? 'ج.م'
                    ]);

                case 'get_stagnant_products':
                    $thirtyDaysAgo = Carbon::now()->subDays(30);
                    // Find products with zero sales in the last 30 days
                    $soldProductIds = Sales::where('vendor_id', $vendorId)
                        ->where('created_at', '>=', $thirtyDaysAgo)
                        ->distinct()
                        ->pluck('product_id');

                    $stagnantProducts = Product::where('vendor_id', $vendorId)
                        ->whereNotIn('id', $soldProductIds)
                        ->where('quantity', '>', 0)
                        ->select('name', 'quantity', 'cost_price', 'selling_price')
                        ->limit(10)
                        ->get();

                    return json_encode(['status' => 'success', 'stagnant_items' => $stagnantProducts]);

                case 'get_expiring_products':
                    $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
                    $query = Product::where('vendor_id', $vendorId)->whereNotNull('expiry_date');
                    
                    if ($isSqlite) {
                        $query->whereRaw('julianday(expiry_date) - julianday(\'now\') <= expiry_alert_days');
                    } else {
                        $query->whereRaw('DATEDIFF(expiry_date, CURDATE()) <= expiry_alert_days');
                    }
                    
                    $products = $query->select('name', 'expiry_date', 'quantity', 'cost_price', 'selling_price')->get();
                    
                    // Calculate potential revenue and loss
                    $potential_loss = 0;
                    $potential_revenue = 0;
                    foreach ($products as $p) {
                        $potential_loss += ($p->cost_price * $p->quantity);
                        $potential_revenue += ($p->selling_price * $p->quantity);
                    }
                    
                    return json_encode([
                        'status' => 'success', 
                        'expiring_items' => $products,
                        'financial_impact' => [
                            'potential_loss_if_expired' => $potential_loss,
                            'potential_revenue_if_sold' => $potential_revenue,
                            'currency' => auth()->user()->vendor->currency ?? 'ج.م'
                        ]
                    ]);

                case 'get_financial_dues':
                    $supplierDues = \App\Models\Purchase::where('vendor_id', $vendorId)
                        ->whereRaw('total_amount > paid_amount')
                        ->with('supplier:id,name,phone')
                        ->select('id', 'supplier_id', 'invoice_number', 'total_amount', 'paid_amount', 'created_at')
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->map(function ($purchase) {
                            return [
                                'type' => 'debt_to_supplier',
                                'invoice_number' => $purchase->invoice_number,
                                'supplier' => $purchase->supplier->name ?? 'غير محدد',
                                'date' => $purchase->created_at->toDateString(),
                                'total_amount' => $purchase->total_amount,
                                'paid_amount' => $purchase->paid_amount,
                                'remaining_due' => $purchase->total_amount - $purchase->paid_amount,
                            ];
                        });
                        
                    $clientDues = Invoice::where('vendor_id', $vendorId)
                        ->whereRaw('total_amount > paid_amount')
                        ->with('client:id,name,phone')
                        ->select('id', 'client_id', 'invoice_code', 'total_amount', 'paid_amount', 'created_at')
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->map(function ($invoice) {
                            return [
                                'type' => 'credit_from_client',
                                'invoice_number' => $invoice->invoice_code,
                                'client' => $invoice->client->name ?? 'غير محدد',
                                'date' => $invoice->created_at->toDateString(),
                                'total_amount' => $invoice->total_amount,
                                'paid_amount' => $invoice->paid_amount,
                                'remaining_due' => $invoice->total_amount - $invoice->paid_amount,
                            ];
                        });

                    return json_encode([
                        'status' => 'success', 
                        'what_we_owe_suppliers' => $supplierDues,
                        'what_clients_owe_us' => $clientDues,
                        'summary' => [
                            'total_debts_to_suppliers' => $supplierDues->sum('remaining_due'),
                            'total_credits_from_clients' => $clientDues->sum('remaining_due')
                        ],
                        'currency' => auth()->user()->vendor->currency ?? 'ج.م'
                    ]);

                case 'get_recent_clients':
                    $clients = \App\Models\Client::where('vendor_id', $vendorId)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->select('name', 'phone', 'created_at')
                        ->get();
                    return json_encode(['status' => 'success', 'recent_clients' => $clients]);

                case 'get_recent_returns':
                    $returns = \App\Models\CustomerReturn::with(['product:id,name', 'invoice:id,invoice_code'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function($ret) {
                            return [
                                'product_name' => $ret->product->name ?? 'غير محدد',
                                'invoice_code' => $ret->invoice->invoice_code ?? 'غير محدد',
                                'quantity_returned' => $ret->quantity_returned,
                                'return_amount' => $ret->return_amount,
                                'date' => $ret->created_at->toDateString()
                            ];
                        });
                    return json_encode([
                        'status' => 'success', 
                        'recent_returns' => $returns,
                        'currency' => auth()->user()->vendor->currency ?? 'ج.م'
                    ]);

                case 'get_recent_invoices':
                    $invoices = Invoice::where('vendor_id', $vendorId)
                        ->with('client:id,name')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->select('id', 'client_id', 'total_amount', 'paid_amount', 'created_at')
                        ->get();
                    return json_encode(['status' => 'success', 'recent_invoices' => $invoices]);

                default:
                    return json_encode(['error' => 'Function not found.']);
            }
        } catch (\Exception $e) {
            Log::error("AiAgentService Tool Error ($functionName): " . $e->getMessage());
            return json_encode(['error' => 'Internal server error while executing tool.']);
        }
    }

    /**
     * Process a chat message using the Gemini Native API structure (fallback for proxies)
     */
    public function ask(array $messages)
    {
        // Route to Native Gemini only if using the specific Google native endpoint
        $isNativeGemini = str_contains($this->apiUrl, ':generateContent');

        if ($isNativeGemini) {
            return $this->askGemini($messages);
        }

        return $this->askOpenAI($messages);
    }

    protected function askOpenAI(array $messages)
    {
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ];

        // --- Smart Payload Adapter ---
        // Some proxies (like mse_ai_api) are strict about the OpenAI schema 
        // and require the 'parameters' key even if empty. 
        // Official Google OpenAI bridge hates it. We adapt on the fly.
        $isProxy = !str_contains($this->apiUrl, 'googleapis.com');
        $tools = $this->getTools();
        
        if ($isProxy) {
            $tools = array_map(function($tool) {
                if ($tool['type'] === 'function' && !isset($tool['function']['parameters'])) {
                    $tool['function']['parameters'] = ['type' => 'object', 'properties' => (object)[]];
                }
                return $tool;
            }, $tools);
        }

        // mse_ai_api uses browser automation, which can take longer than a normal API.
        // We increase the timeout to 120 seconds to prevent cURL error 28.
        $response = Http::withHeaders($headers)->timeout(120)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'tools' => !empty($tools) ? $tools : null,
            'tool_choice' => !empty($tools) ? 'auto' : null,
        ]);

        if ($response->failed()) {
            $errorBody = $response->body();
            
            // Helpful hints for browser-based proxies (mse_ai_api / mse_ai_g)
            if (str_contains($errorBody, 'Execution context was destroyed') || str_contains($errorBody, 'DOM.describeNode')) {
                throw new \Exception('AI Proxy Error: The browser session crashed. Please RESTART the AI Proxy script (mse_ai_api) on your server.');
            }
            
            throw new \Exception('OpenAI API Error: ' . $errorBody);
        }

        $responseData = $response->json();
        $responseMessage = $responseData['choices'][0]['message'];

        if (empty($responseMessage['tool_calls'])) {
            // FALLBACK FOR PROXIES THAT LEAK JSON IN TEXT
            $content = $responseMessage['content'] ?? '';
            if (preg_match('/"name"\s*:\s*"([^"]+)"/', $content, $matches)) {
                $functionName = $matches[1];
                $validTools = array_column(array_column($tools, 'function'), 'name');
                
                if (in_array($functionName, $validTools)) {
                    $functionArgs = [];
                    // Try to extract JSON arguments
                    if (preg_match('/"arguments"\s*:\s*(\{.*?\})/s', $content, $argMatches)) {
                        $functionArgs = json_decode($argMatches[1], true) ?? [];
                    } elseif (preg_match('/"arguments"\s*:\s*"(.*?)"/s', $content, $argMatches)) {
                        $decoded = str_replace(['\"', "\\'"], ['"', "'"], $argMatches[1]);
                        $functionArgs = json_decode($decoded, true) ?? [];
                    }
                    
                    // Emulate tool_call structure
                    $responseMessage['tool_calls'] = [[
                        'id' => 'call_' . uniqid(),
                        'type' => 'function',
                        'function' => [
                            'name' => $functionName,
                            'arguments' => json_encode($functionArgs)
                        ]
                    ]];
                    
                    // Clean up the content so the user doesn't see the raw JSON if the AI fails later
                    $responseMessage['content'] = preg_replace('/```json\s*\{.*?"name".*?\}\s*```/s', '', $content);
                    $responseMessage['content'] = preg_replace('/\{.*?"name"\s*:\s*"([^"]+)".*?\}/s', '', $responseMessage['content']);
                }
            }
            
            if (empty($responseMessage['tool_calls'])) {
                return $responseMessage;
            }
        }

        $messages[] = $responseMessage;
        foreach ($responseMessage['tool_calls'] as $toolCall) {
            $functionName = $toolCall['function']['name'];
            $functionArgs = json_decode($toolCall['function']['arguments'], true) ?? [];
            $functionResponse = $this->executeTool($functionName, $functionArgs);
            $messages[] = [
                'tool_call_id' => $toolCall['id'],
                'role' => 'tool',
                'name' => $functionName,
                'content' => $functionResponse,
            ];
        }

        $finalResponse = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'tools' => !empty($tools) ? $tools : null,
            'tool_choice' => 'auto'
        ]);

        $finalData = $finalResponse->json();
        
        if (!isset($finalData['choices'][0]['message'])) {
            Log::error('AI API Error Response: ' . json_encode($finalData));
            throw new \Exception('Unexpected AI Response Format');
        }

        return $finalData['choices'][0]['message'];
    }

    protected function askGemini(array $messages)
    {
        // Convert messages to Gemini format (contents/parts)
        $contents = [];
        $systemText = '';

        // Extract system instruction first
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemText .= $msg['content'] . "\n\n";
            }
        }

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') continue;
            
            $text = $msg['content'];
            // Prepend system instruction to the very first user message to guarantee compatibility with all models
            if ($systemText && $msg['role'] !== 'assistant' && $msg['role'] !== 'tool' && empty($contents)) {
                $text = "System Rules:\n" . $systemText . "User Message:\n" . $text;
            }

            $contents[] = [
                'role' => ($msg['role'] === 'assistant' || $msg['role'] === 'tool') ? 'model' : 'user',
                'parts' => [['text' => $text]]
            ];
        }

        $payload = [
            'contents' => $contents,
            'tools' => [['function_declarations' => array_map(fn($t) => $t['function'], $this->getTools())]],
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $url = $this->apiUrl;

        // Google Direct API uses x-goog-api-key, OpenAI/Proxies use Authorization: Bearer
        if (str_contains($url, 'generativelanguage')) {
            $headers['x-goog-api-key'] = $this->apiKey;
            if (!str_contains($url, 'key=')) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'key=' . $this->apiKey;
            }
        } else {
            $headers['Authorization'] = "Bearer {$this->apiKey}";
        }

        $response = Http::withHeaders($headers)->timeout(45)->post($url, $payload);

        // Smart Fallback: If 404 Model Not Found, automatically try alternative models
        if ($response->status() === 404 && str_contains($url, 'generativelanguage')) {
            $fallbackModels = ['gemini-2.0-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro'];
            
            foreach ($fallbackModels as $fallbackModel) {
                // Dynamically replace the model name in the URL
                $newUrl = preg_replace('/models\/[^\:]+:/', "models/{$fallbackModel}:", $url);
                
                if ($newUrl !== $url) {
                    $retryResponse = Http::withHeaders($headers)->timeout(45)->post($newUrl, $payload);
                    if ($retryResponse->successful() || $retryResponse->status() !== 404) {
                        $response = $retryResponse;
                        $url = $newUrl; // Keep the working URL for the second tool call later
                        break;
                    }
                }
            }
        }

        if ($response->failed()) {
            throw new \Exception('Gemini API Error: ' . $response->body());
        }

        $responseData = $response->json();
        
        // Handle Gemini response structure
        $candidate = $responseData['candidates'][0] ?? null;
        if (!$candidate) return ['role' => 'assistant', 'content' => 'No response from Gemini.'];

        $modelMessage = $candidate['content'] ?? null;
        $parts = $modelMessage['parts'] ?? [];
        
        $text = '';
        $toolCalls = [];
        foreach ($parts as $part) {
            if (isset($part['text'])) $text .= $part['text'];
            if (isset($part['functionCall'])) $toolCalls[] = $part['functionCall'];
        }

        if (empty($toolCalls)) {
            return ['role' => 'assistant', 'content' => $text];
        }

        // Handle tool calls for Gemini
        foreach ($toolCalls as $call) {
            $functionName = $call['name'];
            $functionArgs = $call['args'] ?? [];
            $functionResponse = $this->executeTool($functionName, $functionArgs);
            
            $contents[] = $modelMessage;
            $contents[] = [
                'role' => 'function',
                'parts' => [
                    'functionResponse' => [
                        'name' => $functionName,
                        'response' => ['content' => $functionResponse]
                    ]
                ]
            ];
        }

        // Re-call with tool results
        $response = Http::withHeaders($headers)->timeout(45)->post($url, ['contents' => $contents]);

        $finalData = $response->json();
        $finalParts = $finalData['candidates'][0]['content']['parts'] ?? [['text' => 'Error processing tool output']];
        
        return ['role' => 'assistant', 'content' => $finalParts[0]['text'] ?? ''];
    }
}
