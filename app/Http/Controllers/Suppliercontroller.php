<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class Suppliercontroller extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    
    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        // Load the supplier with their purchases
        $supplier->load('purchases');
    
        // Calculate total purchases amount, total change, and total paid amount
        $totalPurchases = $supplier->purchases->sum('total_amount');
        $totalChange = $supplier->purchases->sum('change');
        $totalPaidAmount = $supplier->purchases->sum('paid_amount');
    
        return view('admin.suppliers.show', compact('supplier', 'totalPurchases', 'totalChange', 'totalPaidAmount'));
    }
    
    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        Supplier::create($validated);

        return response()->json(['success' => true, 'message' => 'Supplier created successfully']);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json(['success' => true, 'message' => 'Supplier updated successfully']);
    }

    /**
     * Remove the specified supplier from the database.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد بنجاح.');
    }
}
