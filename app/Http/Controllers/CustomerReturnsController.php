<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerReturn;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Sales;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerReturnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = CustomerReturn::with(['invoice', 'product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.returns.create');
    }

    /**
     * Show return form for specific invoice
     */
    public function createForInvoice(Invoice $invoice)
    {
        $invoice->load(['sales.product', 'client']);

        // Get sales items that haven't been fully returned
        $availableItems = $invoice->sales->map(function ($sale) {
            $alreadyReturned = CustomerReturn::where('invoice_id', $sale->invoice_id)
                ->where('product_id', $sale->product_id)
                ->sum('quantity_returned');

            $sale->available_for_return = $sale->quantity - $alreadyReturned;
            return $sale;
        })->filter(function ($sale) {
            return $sale->available_for_return > 0;
        });

        return view('admin.returns.create-for-invoice', compact('invoice', 'availableItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Filter out items with quantity < 1 before validation
        $filteredReturns = collect($request->input('returns', []))
            ->filter(function($item) {
                return isset($item['quantity']) && $item['quantity'] >= 1;
            })->values()->all();

        // Overwrite the returns input with filtered items
        $request->merge(['returns' => $filteredReturns]);

        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'returns' => 'required|array|min:1',
            'returns.*.product_id' => 'required|exists:products,id',
            'returns.*.quantity' => 'required|integer|min:1',
            'returns.*.reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($request->invoice_id);
            $totalReturnAmount = 0;

            foreach ($request->returns as $returnData) {
                $product = Product::findOrFail($returnData['product_id']);
                $quantity = $returnData['quantity'];

                // Find the original sale
                $sale = Sales::where('invoice_id', $invoice->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$sale) {
                    throw new \Exception("المنتج غير موجود في هذه الفاتورة");
                }

                // Check available quantity for return
                $alreadyReturned = CustomerReturn::where('invoice_id', $invoice->id)
                    ->where('product_id', $product->id)
                    ->sum('quantity_returned');

                $availableForReturn = $sale->quantity - $alreadyReturned;

                if ($quantity > $availableForReturn) {
                    throw new \Exception("الكمية المطلوب إرجاعها أكبر من المتاحة للمنتج: " . $product->name);
                }

                // Calculate return amount
                $returnAmount = ($sale->total_price / $sale->quantity) * $quantity;

                // Create customer return record
                CustomerReturn::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity_returned' => $quantity,
                    'reason' => $returnData['reason'],
                    'return_amount' => $returnAmount,
                    'user_id' => Auth::id(),
                    'status' => 'completed'
                ]);

                // Return products to inventory using FIFO logic
                $this->returnProductsToInventory($sale, $quantity);

                $totalReturnAmount += $returnAmount;
            }

            // Update invoice totals
            $this->updateInvoiceTotals($invoice, $totalReturnAmount);

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'تمت عملية الإرجاع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerReturn $customerReturn)
    {
        $customerReturn->load(['invoice.client', 'product', 'user']);
        return view('admin.returns.show', compact('customerReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerReturn $customerReturn)
    {
        return view('admin.returns.edit', compact('customerReturn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerReturn $customerReturn)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $customerReturn->update([
            'reason' => $request->reason,
            'status' => $request->status
        ]);

        return redirect()->route('customer-returns.show', $customerReturn)
            ->with('success', 'تم تحديث بيانات الإرجاع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerReturn $customerReturn)
    {
        // This should only be allowed in special cases
        // You might want to add additional checks here

        DB::beginTransaction();

        try {
            // Reverse the inventory changes
            $this->reverseInventoryChanges($customerReturn);

            // Update invoice totals
            $this->updateInvoiceTotals($customerReturn->invoice, -$customerReturn->return_amount);

            $customerReturn->delete();

            DB::commit();

            return redirect()->route('customer-returns.index')
                ->with('success', 'تم حذف الإرجاع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Return products to inventory using FIFO logic
     */
    private function returnProductsToInventory($sale, $quantityToReturn)
    {
        // If we have purchase_product_id, return to that specific batch
        if ($sale->purchase_product_id) {
            $purchaseProduct = PurchaseProduct::find($sale->purchase_product_id);
            if ($purchaseProduct) {
                $purchaseProduct->increment('remaining_quantity', $quantityToReturn);
            }
        }

        // Also update the main product quantity
        $sale->product->increment('quantity', $quantityToReturn);
    }

    /**
     * Update invoice totals after return
     */
    private function updateInvoiceTotals($invoice, $returnAmount)
    {
        $invoice->subtotal -= $returnAmount;
        $invoice->total_amount = $invoice->subtotal - $invoice->discount;

        // Recalculate change
        $invoice->change = $invoice->total_amount - $invoice->paid_amount;

        $invoice->save();
    }

    /**
     * Reverse inventory changes (for return deletion)
     */
    private function reverseInventoryChanges($customerReturn)
    {
        $sale = Sales::where('invoice_id', $customerReturn->invoice_id)
            ->where('product_id', $customerReturn->product_id)
            ->first();

        if ($sale && $sale->purchase_product_id) {
            $purchaseProduct = PurchaseProduct::find($sale->purchase_product_id);
            if ($purchaseProduct) {
                $purchaseProduct->decrement('remaining_quantity', $customerReturn->quantity_returned);
            }
        }

        // Also update the main product quantity
        $customerReturn->product->decrement('quantity', $customerReturn->quantity_returned);
    }

    /**
     * Search returns
     */
    public function search(Request $request)
    {
        $query = CustomerReturn::with(['invoice', 'product', 'user']);

        if ($request->invoice_code) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->where('invoice_code', 'like', '%' . $request->invoice_code . '%');
            });
        }

        if ($request->product_name) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product_name . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }
}
