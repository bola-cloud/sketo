<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CashierController;

class SyncController extends Controller
{
    /**
     * Handle incoming batch of offline actions
     */
    public function process(Request $request)
    {
        $actions = $request->input('actions', []);
        $results = [];

        foreach ($actions as $action) {
            try {
                $results[] = $this->handleAction($action);
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $action['id'] ?? null,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json($results);
    }

    protected function handleAction($action)
    {
        $url = $action['url'];
        $data = $action['data'];

        // 1. Handle Cashier Checkout
        if (str_contains($url, '/cashier/checkout')) {
            return $this->handleCashierSync($action);
        }

        // 2. Handle CRUD operations (Example patterns)
        if (str_contains($url, '/products/store')) {
            return $this->handleProductStore($data);
        }

        if (str_contains($url, '/clients/store')) {
            return $this->handleClientStore($data);
        }

        // Generic fallback or unsupported
        return [
            'status' => 'unsupported',
            'url' => $url
        ];
    }

    protected function handleCashierSync($action)
    {
        $cashier = new CashierController();
        // Since we refactored checkout to processCheckoutLogic, we should ideally call that.
        // But for now, we can use the existing syncOfflineInvoice endpoint-like logic
        $data = $action['data'];
        $uuid = 'OFFLINE-' . ($action['id'] ?? uniqid());

        // Call the internal logic via Request simulation or refactoring
        $request = new Request($action);
        return $cashier->syncOfflineInvoice($request)->getData();
    }

    protected function handleProductStore($data)
    {
        $product = Product::create($data);
        return ['status' => 'success', 'model' => 'Product', 'id' => $product->id];
    }

    protected function handleClientStore($data)
    {
        $client = Client::create($data);
        return ['status' => 'success', 'model' => 'Client', 'id' => $client->id];
    }
}
