<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\SalesInstallment;

class SalesInstallmentController extends Controller
{
    // Show all installments for a given invoice
    public function indexInstallments($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installments = $invoice->installments()->get();

        return view('admin.invoices.installments', compact('invoice', 'installments'));
    }

    // Store a new installment for a given invoice
    public function storeInstallment(Request $request, $invoiceId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
        ]);
    
        // Find the invoice by ID or fail if not found
        $invoice = Invoice::findOrFail($invoiceId);
    
        // Create a new installment
        SalesInstallment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => $request->input('amount_paid'),
            'date_paid' => $request->input('date_paid'),
        ]);
    
        // Recalculate the total paid amount for the invoice
        $totalPaid = SalesInstallment::where('invoice_id', $invoice->id)->sum('amount_paid');
    
        // Update the paid_amount and change fields in the invoice
        $invoice->update([
            'paid_amount' => $totalPaid,
            'change' => $invoice->total_amount - $totalPaid,
        ]);
    
        // Redirect back to the installments page with success message
        return redirect()->route('sales.installments.index', $invoice->id)->with('success', 'تمت إضافة القسط بنجاح وتم تحديث الفاتورة.');
    }
    
    // Show the form to edit an installment
    public function editInstallment($invoiceId, $installmentId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = SalesInstallment::findOrFail($installmentId);

        return view('admin.invoices.edit-installment', compact('invoice', 'installment'));
    }

    // Update the installment
    public function updateInstallment(Request $request, $invoiceId, $installmentId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
        ]);

        // Find the invoice and installment
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = SalesInstallment::findOrFail($installmentId);

        // Update the installment with the new values
        $installment->update([
            'amount_paid' => $request->input('amount_paid'),
            'date_paid' => $request->input('date_paid'),
        ]);

        // Recalculate the total paid amount for the invoice
        $totalPaid = SalesInstallment::where('invoice_id', $invoice->id)->sum('amount_paid');

        // Update the paid_amount and change fields in the invoice
        $invoice->update([
            'paid_amount' => $totalPaid,
            'change' => $invoice->total_amount - $totalPaid,
        ]);

        // Redirect back to the installments page with success message
        return redirect()->route('sales.installments.index', $invoice->id)->with('success', 'تم تعديل القسط بنجاح.');
    }

}
