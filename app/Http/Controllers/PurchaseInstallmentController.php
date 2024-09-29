<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseInstallment;
use App\Models\Purchase;

class PurchaseInstallmentController extends Controller
{

    public function create($id)
    {
        $purchase = Purchase::find($id);
        return view('admin.purchases.installments', compact('purchase'));
    }

    public function store(Request $request)
    {
        $purchase = Purchase::find($request->input('purchase_id'));
    
        // Validate that the new installment doesn't exceed the unpaid balance
        $validatedData = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'amount_paid' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($purchase) {
                    if ($value > $purchase->change) {
                        $fail('The paid amount cannot exceed the remaining balance.');
                    }
                }
            ],
            'date_paid' => 'required|date',
        ]);
    
        // Create a new installment
        PurchaseInstallment::create($validatedData);
    
        // Sum all the paid installments and calculate the remaining change
        $totalPaid = $purchase->installments()->sum('amount_paid');
        $change = $purchase->total_amount - $totalPaid;
    
        // Update the purchase with the new total paid and change
        $purchase->update([
            'paid_amount' => $totalPaid,  // Sum of installments
            'change' => $change,  // total_amount - total_paid
        ]);
    
        return redirect()->route('purchases.show', $purchase->id)->with('success', 'تمت إضافة الدفعة بنجاح.');
    }
    

    public function destroy(PurchaseInstallment $installment)
    {
        $purchase = $installment->purchase; // Now this will work correctly
    
        if ($purchase) {
            $purchase->load('installments'); // Load installments
    
            // Delete the installment
            $installment->delete();
    
            // Recalculate the total paid and change after deletion
            $totalPaid = $purchase->installments()->sum('amount_paid');
            $change = $purchase->total_amount - $totalPaid;
    
            // Update the purchase with new paid and change values
            $purchase->update([
                'paid_amount' => $totalPaid,
                'change' => $change,
            ]);
    
            return redirect()->route('purchases.show', $purchase->id)->with('success', 'تم حذف الدفعة بنجاح.');
        } else {
            return redirect()->back()->with('error', 'الشراء المرتبط بهذا القسط غير موجود.');
        }
    }    
    
}
