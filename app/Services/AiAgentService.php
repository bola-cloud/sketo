<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Invoice;
use Carbon\Carbon;

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
                    'name' => 'get_today_summary',
                    'description' => 'Get a quick financial summary for today, including total revenue and total products sold.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_low_stock_products',
                    'description' => 'Get a list of products that have reached or dropped below their minimum allowed stock threshold.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_expiring_products',
                    'description' => 'Get a list of products that are about to expire within their defined expiry alert period.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_recent_clients',
                    'description' => 'Get a list of the 5 most recently added clients.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_stagnant_products',
                    'description' => 'Get a list of stagnant products that have had zero sales in the last 30 days.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_purchase_summary',
                    'description' => 'Get a summary of recent purchase invoices (stock incoming).',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_top_selling_products',
                    'description' => 'Identify the top 5 best-selling products by quantity.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                        'required' => [],
                    ],
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
                        'properties' => (object)[],
                        'required' => [],
                    ],
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
                    $today = Carbon::today();
                    $totalSold = Sales::where('vendor_id', $vendorId)
                        ->whereDate('created_at', $today)
                        ->sum('quantity');
                    $totalRevenue = Invoice::where('vendor_id', $vendorId)
                        ->whereDate('created_at', $today)
                        ->sum('paid_amount');
                    
                    return json_encode([
                        'status' => 'success', 
                        'date' => $today->toDateString(),
                        'total_items_sold' => $totalSold, 
                        'total_cash_revenue' => $totalRevenue
                    ]);

                case 'get_low_stock_products':
                    $products = Product::where('vendor_id', $vendorId)
                        ->whereColumn('quantity', '<=', 'threshold')
                        ->select('name', 'quantity', 'threshold')
                        ->get();
                    return json_encode(['status' => 'success', 'low_stock_items' => $products]);

                case 'get_top_selling_products':
                    $topProducts = Sales::where('vendor_id', $vendorId)
                        ->select('product_id', \DB::raw('SUM(quantity) as total_qty'))
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
                    $days = $args['days'] ?? 1;
                    $startDate = Carbon::now()->subDays($days);
                    
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
                        'gross_profit' => $totalRevenue - $totalCost
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
                    
                    $products = $query->select('name', 'expiry_date')->get();
                    return json_encode(['status' => 'success', 'expiring_items' => $products]);

                case 'get_recent_clients':
                    $clients = \App\Models\Client::where('vendor_id', $vendorId)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->select('name', 'phone', 'created_at')
                        ->get();
                    return json_encode(['status' => 'success', 'recent_clients' => $clients]);

                case 'get_recent_invoices':
                    $invoices = Invoice::where('vendor_id', $vendorId)
                        ->with('client:id,name')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->select('id', 'client_id', 'total_amount', 'paid_amount', 'remaining_amount', 'created_at')
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
        $isGemini = str_contains($this->apiUrl, 'generativelanguage') || str_contains($this->apiUrl, 'v1beta');

        if ($isGemini) {
            return $this->askGemini($messages);
        }

        return $this->askOpenAI($messages);
    }

    protected function askOpenAI(array $messages)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(45)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'tools' => $this->getTools(),
            'tool_choice' => 'auto',
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API Error: ' . $response->body());
        }

        $responseData = $response->json();
        $responseMessage = $responseData['choices'][0]['message'];

        if (empty($responseMessage['tool_calls'])) {
            return $responseMessage;
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
        ])->timeout(45)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
        ]);

        $finalData = $finalResponse->json();
        return $finalData['choices'][0]['message'];
    }

    protected function askGemini(array $messages)
    {
        // Convert messages to Gemini format (contents/parts)
        $contents = [];
        $systemInstruction = null;

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemInstruction = ['parts' => [['text' => $msg['content']]]];
                continue;
            }
            
            $contents[] = [
                'role' => ($msg['role'] === 'assistant' || $msg['role'] === 'tool') ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        $payload = [
            'contents' => $contents,
            'tools' => [['function_declarations' => array_map(fn($t) => $t['function'], $this->getTools())]],
        ];

        if ($systemInstruction) {
            $payload['system_instruction'] = $systemInstruction;
        }

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
