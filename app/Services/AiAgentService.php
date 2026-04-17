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
                        'properties' => [],
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
                        'properties' => [],
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
                        'properties' => [],
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
                        'properties' => [],
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
                        'properties' => [],
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
     * Process a chat message using the OpenAI API spec
     */
    public function ask(array $messages)
    {
        // Send initial request with tools
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
            Log::error('AI API Error: ' . $response->body());
            throw new \Exception('Failed to communicate with AI API. Ensure the mse_ai_api proxy is running at ' . $this->apiUrl);
        }

        $responseData = $response->json();
        
        // Ensure successful response shape
        if (!isset($responseData['choices'][0]['message'])) {
            return ['role' => 'assistant', 'content' => 'Error: Improper format received from AI server.'];
        }

        $responseMessage = $responseData['choices'][0]['message'];
        
        // Handle standard response without tool calls
        if (empty($responseMessage['tool_calls'])) {
            return $responseMessage;
        }

        // Handle Tool Calling iteratively
        $messages[] = $responseMessage; // Append the assistant's context of tool request

        foreach ($responseMessage['tool_calls'] as $toolCall) {
            $functionName = $toolCall['function']['name'];
            $functionArgs = json_decode($toolCall['function']['arguments'], true) ?? [];
            
            // Execute the isolated internal tool
            $functionResponse = $this->executeTool($functionName, $functionArgs);
            
            // Provide tool response back to AI
            $messages[] = [
                'tool_call_id' => $toolCall['id'],
                'role' => 'tool',
                'name' => $functionName,
                'content' => $functionResponse,
            ];
        }

        // Second Call: Get final answer with tool data injected
        $finalResponse = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(45)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
        ]);

        if ($finalResponse->failed()) {
            throw new \Exception('AI API failed during tool response synthesis.');
        }

        $finalData = $finalResponse->json();
        return $finalData['choices'][0]['message'];
    }
}
