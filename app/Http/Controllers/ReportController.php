<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialSummaryExport;
use App\Exports\InventoryValuationExport;
use App\Exports\AgingReportExport;

class ReportController extends Controller
{
    // Daily Report
    public function dailyReport()
    {
        $today = Carbon::now()->startOfDay();

        // Get invoices created today
        $invoices = Invoice::whereDate('created_at', $today)
            ->with('sales.product') // Load related sales and products
            ->get();

        // Calculate total revenue, paid amount, and total profit
        $totalRevenue = $invoices->sum('paid_amount'); // Use paid amount
        $totalQuantity = $invoices->flatMap->sales->sum('quantity'); // Total quantity from sales
        $totalProfit = $invoices->sum(function ($invoice) {
            return $invoice->sales->sum(function ($sale) {
                // Profit per sale (selling price - cost price) * quantity
                return ($sale->product->selling_price - $sale->product->cost_price) * $sale->quantity;
            });
        });

        return view('admin.reports.daily', compact('invoices', 'totalQuantity', 'totalRevenue', 'totalProfit'));
    }

    // Monthly Report
    public function monthlyReport()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get invoices created this month
        $invoices = Invoice::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->with('sales.product') // Load related sales and products
            ->get();

        // Calculate total revenue, paid amount, and total profit
        $totalRevenue = $invoices->sum('paid_amount'); // Use paid amount
        $totalQuantity = $invoices->flatMap->sales->sum('quantity'); // Total quantity from sales
        $totalProfit = $invoices->sum(function ($invoice) {
            return $invoice->sales->sum(function ($sale) {
                // Profit per sale (selling price - cost price) * quantity
                return ($sale->product->selling_price - $sale->product->cost_price) * $sale->quantity;
            });
        });

        return view('admin.reports.monthly', compact('invoices', 'totalQuantity', 'totalRevenue', 'totalProfit'));
    }

    // Date Range Report
    public function dateRangeReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Get invoices within the date range
        $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->with('sales.product') // Load related sales and products
            ->get();

        // Calculate total revenue, paid amount, and total profit
        $totalRevenue = $invoices->sum('paid_amount'); // Use paid amount
        $totalQuantity = $invoices->flatMap->sales->sum('quantity'); // Total quantity from sales
        $totalProfit = $invoices->sum(function ($invoice) {
            return $invoice->sales->sum(function ($sale) {
                // Profit per sale (selling price - cost price) * quantity
                return ($sale->product->selling_price - $sale->product->cost_price) * $sale->quantity;
            });
        });

        return view('admin.reports.date_report', compact('invoices', 'totalQuantity', 'totalRevenue', 'totalProfit', 'startDate', 'endDate'));
    }

    public function productsSoldDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sales::with('product', 'invoice');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $sales = $query->latest()->paginate(20);
        return view('admin.reports.statistics.products_sold', compact('sales', 'startDate', 'endDate'));
    }

    public function revenueDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Invoice::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $invoices = $query->latest()->paginate(20);
        return view('admin.reports.statistics.revenue', compact('invoices', 'startDate', 'endDate'));
    }

    public function inventoryDetails()
    {
        $products = \App\Models\Product::with('brand', 'category')->paginate(20);
        return view('admin.reports.statistics.inventory', compact('products'));
    }

    public function purchasesDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = \App\Models\Purchase::with('supplier');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $purchases = $query->latest()->paginate(20);
        return view('admin.reports.statistics.purchases', compact('purchases', 'startDate', 'endDate'));
    }

    public function profitDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sales::with('product', 'invoice')
            ->join('purchase_products', 'sales.purchase_product_id', '=', 'purchase_products.id')
            ->select('sales.*', DB::raw('(sales.total_price - (sales.quantity * purchase_products.cost_price)) as profit'));

        if ($startDate && $endDate) {
            $query->whereBetween('sales.created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $sales = $query->latest('sales.created_at')->paginate(20);
        return view('admin.reports.statistics.profit', compact('sales', 'startDate', 'endDate'));
    }

    public function cashFlowDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesInstallmentsQuery = \App\Models\SalesInstallment::query();
        $purchaseInstallmentsQuery = \App\Models\PurchaseInstallment::query();

        if ($startDate && $endDate) {
            $sd = Carbon::parse($startDate)->startOfDay();
            $ed = Carbon::parse($endDate)->endOfDay();
            $salesInstallmentsQuery->whereBetween('date_paid', [$sd, $ed]);
            $purchaseInstallmentsQuery->whereBetween('date_paid', [$sd, $ed]);
        }

        $salesInstallments = $salesInstallmentsQuery->latest('date_paid')->get();
        $purchaseInstallments = $purchaseInstallmentsQuery->latest('date_paid')->get();

        return view('admin.reports.statistics.cash_flow', compact('salesInstallments', 'purchaseInstallments', 'startDate', 'endDate'));
    }

    public function financialSummary(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // 1. Revenue (Fully & Partially Paid Invoices)
        $revenue = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');

        // 2. COGS (Cost of Goods Sold)
        $cogs = Sales::whereBetween('sales.created_at', [$startDate, $endDate])
            ->join('purchase_products', 'sales.purchase_product_id', '=', 'purchase_products.id')
            ->sum(DB::raw('sales.quantity * purchase_products.cost_price'));

        // 3. Gross Profit
        $grossProfit = $revenue - $cogs;

        // 4. Operating Expenses (Purchases of type 'expense')
        $operatingExpenses = \App\Models\Purchase::where('type', 'expense')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // 5. Net Profit
        $netProfit = $grossProfit - $operatingExpenses;
        if ($request->has('export')) {
            return Excel::download(new FinancialSummaryExport($revenue, $cogs, $grossProfit, $operatingExpenses, $netProfit, $startDate, $endDate), 'Financial_Summary_' . now()->format('Y-m-d') . '.xlsx');
        }

        return view('admin.reports.statistics.financial_summary', compact(
            'revenue',
            'cogs',
            'grossProfit',
            'operatingExpenses',
            'netProfit',
            'startDate',
            'endDate'
        ));
    }

    public function inventoryValuation()
    {
        $products = \App\Models\Product::where('quantity', '>', 0)->get();

        $totalCostValue = $products->sum(function ($p) {
            return $p->quantity * $p->cost_price;
        });

        $totalRetailValue = $products->sum(function ($p) {
            return $p->quantity * $p->selling_price;
        });

        $potentialProfit = $totalRetailValue - $totalCostValue;
        if (request()->has('export')) {
            return Excel::download(new InventoryValuationExport(), 'Inventory_Valuation_' . now()->format('Y-m-d') . '.xlsx');
        }

        return view('admin.reports.statistics.inventory_valuation', compact(
            'products',
            'totalCostValue',
            'totalRetailValue',
            'potentialProfit'
        ));
    }

    public function agingReport()
    {
        // Accounts Receivable (Clients who owe us)
        $receivables = Invoice::with('client')
            ->where('change', '>', 0)
            ->get();

        // Accounts Payable (Suppliers we owe)
        $payables = \App\Models\Purchase::with('supplier')
            ->where('change', '>', 0)
            ->get();

        $view = view('admin.reports.statistics.aging_report', compact('receivables', 'payables'));

        if (request()->has('export')) {
            return Excel::download(new AgingReportExport(), 'Aging_Report_' . now()->format('Y-m-d') . '.xlsx');
        }

        return $view;
    }
}