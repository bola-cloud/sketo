<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInstallment;
use App\Models\PurchaseInstallment;

class TreasuryController extends Controller
{
    public function treasury(Request $request)
    {
        // Validate the date input or default to today's date
        $date = $request->input('date', now()->format('Y-m-d'));

        // Fetch sales installments for the selected day
        $salesInstallments = SalesInstallment::whereDate('date_paid', $date)->sum('amount_paid');

        // Fetch purchase installments for the selected day
        $purchaseInstallments = PurchaseInstallment::whereDate('date_paid', $date)->sum('amount_paid');

        // Calculate the difference
        $difference = $salesInstallments - $purchaseInstallments;

        // Pass the data to the view
        return view('admin.treasury.index', compact('date', 'salesInstallments', 'purchaseInstallments', 'difference'));
    }
}
