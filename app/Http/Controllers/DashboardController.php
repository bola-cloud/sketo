<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\SalesInstallment;
use App\Models\PurchaseInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $salesQuery = Sales::query();
            $purchaseQuery = Purchase::query();
            $invoiceQuery = Invoice::query();
            $salesInstallmentQuery = SalesInstallment::query(); // New query for Sales Installments
            $purchaseInstallmentQuery = PurchaseInstallment::query(); // New query for Purchase Installments

            // Apply date range filter if provided
            if ($startDate && $endDate) {
                $salesQuery->whereBetween('sales.created_at', [$startDate, $endDate]);
                $purchaseQuery->whereBetween('purchases.created_at', [$startDate, $endDate]);
                $invoiceQuery->whereBetween('invoices.created_at', [$startDate, $endDate]);
                $salesInstallmentQuery->whereBetween('date_paid', [$startDate, $endDate]);
                $purchaseInstallmentQuery->whereBetween('date_paid', [$startDate, $endDate]);
            }

            // Calculate total products sold
            $productsSold = $salesQuery->sum('quantity');

            // Calculate total revenue from invoices
            $totalRevenue = $invoiceQuery->sum('paid_amount');

            // Calculate total unsold products (quantity in stock)
            $totalUnsoldProducts = Product::where('quantity', '>', 0)->sum('quantity');

            // Calculate total purchases as sum of total_amount in purchases table
            $totalPurchases = $purchaseQuery->sum('total_amount');

            // Calculate total profit (Revenue - Cost of Goods Sold)
            $totalProfit = Sales::join('purchase_products', 'sales.purchase_product_id', '=', 'purchase_products.id')
                ->sum(DB::raw('(sales.total_price - (sales.quantity * purchase_products.cost_price))'));

            // Adjust profit by subtracting invoice discounts
            $totalInvoiceDiscount = $invoiceQuery->sum('discount');
            $totalProfit = $totalProfit - $totalInvoiceDiscount;

            // Calculate total sales installments and purchase installments for cash flow
            $totalSalesInstallments = $salesInstallmentQuery->sum('amount_paid');
            $totalPurchaseInstallments = $purchaseInstallmentQuery->sum('amount_paid');

            // Calculate the amount of money the user has (Net Cash flow)
            $availableMoney = $totalSalesInstallments - $totalPurchaseInstallments;


            // Prepare data for the charts (products sold and total revenue per month)
            $monthlyData = Sales::selectRaw('MONTH(sales.created_at) as month, YEAR(sales.created_at) as year, SUM(sales.quantity) as total_sold, SUM(sales.total_price) as total_revenue')
                ->groupBy('month', 'year')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            $lowStockProducts = Product::whereColumn('quantity', '<=', 'threshold')->get();

            $expiringProducts = Product::whereNotNull('expiry_date')
                ->whereRaw('DATEDIFF(expiry_date, CURDATE()) <= expiry_alert_days')
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'productsSold' => $productsSold,
                    'totalRevenue' => $totalRevenue,
                    'totalUnsoldProducts' => $totalUnsoldProducts,
                    'totalPurchases' => $totalPurchases,
                    'totalProfit' => $totalProfit ?? 0,
                    'lowStockProducts' => $lowStockProducts,
                    'expiringProducts' => $expiringProducts,
                    'availableMoney' => $availableMoney, // Include available money
                ]);
            }

            // Return the view with the data
            return view('admin.dashboard', compact(
                'productsSold',
                'totalRevenue',
                'totalUnsoldProducts',
                'totalPurchases',
                'totalProfit',
                'monthlyData',
                'lowStockProducts',
                'expiringProducts',
                'availableMoney' // Include available money
            ));
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

