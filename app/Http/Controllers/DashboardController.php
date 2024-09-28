<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
    
            $query = Sales::query();
            $purchaseQuery = Purchase::query();
    
            // Check if dates are provided
            if ($startDate && $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
                $purchaseQuery->whereBetween('purchases.created_at', [$startDate, $endDate]);
            }
    
            // Calculate total products sold
            $productsSold = $query->sum('quantity');
    
            // Calculate total revenue
            $totalRevenue = $query->sum('total_price');
    
            // Calculate total unsold products (quantity in stock)
            $totalUnsoldProducts = Product::where('quantity', '>', 0)->sum('quantity');
    
            // Calculate total purchases as sum of total_amount in purchases table
            $totalPurchases = $purchaseQuery->sum('total_amount');
    
            // Calculate total profit
            $totalProfit = $query->join('products', 'sales.product_id', '=', 'products.id')
                ->selectRaw('COALESCE(SUM(sales.total_price - (products.cost_price * sales.quantity)), 0) as profit')
                ->value('profit');
    
            // Prepare data for the charts (products sold and total revenue per month)
            $monthlyData = Sales::selectRaw('MONTH(sales.created_at) as month, YEAR(sales.created_at) as year, SUM(sales.quantity) as total_sold, SUM(sales.total_price) as total_revenue')
                ->groupBy('month', 'year')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });
    
            $lowStockProducts = Product::whereColumn('quantity', '<=', 'threshold')->get();
    
            if ($request->ajax()) {
                return response()->json([
                    'productsSold' => $productsSold,
                    'totalRevenue' => $totalRevenue,
                    'totalUnsoldProducts' => $totalUnsoldProducts,
                    'totalPurchases' => $totalPurchases,
                    'totalProfit' => $totalProfit ?? 0,  // Ensure profit is not null
                    'lowStockProducts' => $lowStockProducts,
                ]);
            }
    
            // Return the view with the data
            return view('admin.dashboard', compact('productsSold', 'totalRevenue', 'totalUnsoldProducts', 'totalPurchases', 'totalProfit', 'monthlyData', 'lowStockProducts'));
        } catch (\Exception $e) {
            \Log::error('DashboardController Error: ' . $e->getMessage(), [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'stackTrace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    
    
}

