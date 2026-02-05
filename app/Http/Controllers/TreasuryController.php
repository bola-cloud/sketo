<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInstallment;
use App\Models\PurchaseInstallment;
use App\Models\SupplierReturn;
use App\Models\CustomerReturn;

class TreasuryController extends Controller
{
    public function treasury(Request $request)
    {
        // Validate the date input or default to today's date
        $date = $request->input('date', now()->format('Y-m-d'));

        // Fetch sales installments for the selected day
        $salesInstallments = SalesInstallment::whereDate('date_paid', $date)->sum('amount_paid');
        $salesDetails = SalesInstallment::with(['invoice.client'])
            ->whereDate('date_paid', $date)
            ->get();

        // Fetch purchase installments for the selected day
        $purchaseInstallments = PurchaseInstallment::whereDate('date_paid', $date)->sum('amount_paid');
        $purchaseDetails = PurchaseInstallment::with(['purchase.supplier'])
            ->whereDate('date_paid', $date)
            ->get();

        // Fetch supplier returns (expenses reduced)
        $supplierReturns = SupplierReturn::with(['supplier', 'purchase'])
            ->whereDate('returned_at', $date)
            ->completed()
            ->get();
        $supplierReturnsTotal = $supplierReturns->sum(function($r) { return $r->getTotalValueAttribute(); });

        // Fetch customer returns (income reduced)
        $customerReturns = CustomerReturn::with(['invoice', 'product'])
            ->whereHas('invoice', function($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->where('status', 'completed')
            ->get();
        $customerReturnsTotal = $customerReturns->sum('return_amount');

        // Calculate the difference (sales - customer returns) - (purchases - supplier returns)
        $difference = ($salesInstallments - $customerReturnsTotal) - ($purchaseInstallments - $supplierReturnsTotal);

        // Pass the data to the view
        return view('admin.treasury.index', compact(
            'date',
            'salesInstallments',
            'purchaseInstallments',
            'difference',
            'purchaseDetails',
            'salesDetails',
            'supplierReturns',
            'supplierReturnsTotal',
            'customerReturns',
            'customerReturnsTotal'
        ));
    }
}
