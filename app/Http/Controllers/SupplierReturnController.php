<?php

namespace App\Http\Controllers;

use App\Models\SupplierReturn;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = SupplierReturn::with(['product', 'supplier', 'purchase'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.supplier-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $purchases = Purchase::with('supplier')->where('type', 'product')->get();

        return view('admin.supplier-returns.create', compact('products', 'suppliers', 'purchases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_product_id' => 'nullable|exists:purchase_products,id',
            'quantity_returned' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);

        // If a specific purchase product is selected, use that batch
        if ($request->purchase_product_id) {
            $purchaseProduct = PurchaseProduct::findOrFail($request->purchase_product_id);

            // Validate that this purchase product belongs to the selected supplier
            if ($purchaseProduct->purchase->supplier_id != $request->supplier_id) {
                return back()->withErrors(['purchase_product_id' => 'Selected batch does not belong to the selected supplier.']);
            }

            // Check if we have enough quantity in this specific batch
            if ($purchaseProduct->remaining_quantity < $request->quantity_returned) {
                return back()->withErrors(['quantity_returned' => 'Not enough quantity available in this batch for return.']);
            }

            DB::transaction(function () use ($request, $product, $purchaseProduct) {
                // Create the return record
                $return = SupplierReturn::create([
                    'product_id' => $request->product_id,
                    'supplier_id' => $request->supplier_id,
                    'purchase_id' => $purchaseProduct->purchase_id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'quantity_returned' => $request->quantity_returned,
                    'cost_price' => $request->cost_price,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'status' => 'completed',
                    'returned_at' => now()
                ]);

                // Reduce the specific batch quantity
                $purchaseProduct->reduceStock($request->quantity_returned);

                // Also reduce the product's total quantity
                $product->decrement('quantity', $request->quantity_returned);
            });
        } else {
            // Auto-select from available stock from this supplier (FIFO)
            $availableStock = $product->getStockFromSupplier($request->supplier_id);
            $totalAvailable = $availableStock->sum('remaining_quantity');

            if ($totalAvailable < $request->quantity_returned) {
                return back()->withErrors(['quantity_returned' => 'Not enough quantity available from this supplier for return.']);
            }

            DB::transaction(function () use ($request, $product, $availableStock) {
                $remainingToReturn = $request->quantity_returned;
                $returns = [];

                foreach ($availableStock as $batch) {
                    if ($remainingToReturn <= 0)
                        break;

                    $quantityFromThisBatch = min($remainingToReturn, $batch->remaining_quantity);

                    // Create return record for this batch
                    $return = SupplierReturn::create([
                        'product_id' => $request->product_id,
                        'supplier_id' => $request->supplier_id,
                        'purchase_id' => $batch->purchase_id,
                        'purchase_product_id' => $batch->id,
                        'quantity_returned' => $quantityFromThisBatch,
                        'cost_price' => $batch->cost_price, // Use original cost price from this batch
                        'reason' => $request->reason,
                        'notes' => $request->notes,
                        'status' => 'completed',
                        'returned_at' => now()
                    ]);

                    // Reduce stock from this batch
                    $batch->reduceStock($quantityFromThisBatch);

                    $remainingToReturn -= $quantityFromThisBatch;
                    $returns[] = $return;
                }

                // Reduce the product's total quantity
                $product->decrement('quantity', $request->quantity_returned);
            });
        }

        return redirect()->route('supplier-returns.index')
            ->with('success', 'Supplier return created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load(['product', 'supplier', 'purchase']);
        return view('admin.supplier-returns.show', compact('supplierReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status === 'completed') {
            return redirect()->route('supplier-returns.index')
                ->with('error', 'Cannot edit completed returns.');
        }

        $products = Product::all();
        $suppliers = Supplier::all();
        $purchases = Purchase::with('supplier')->where('type', 'product')->get();

        return view('admin.supplier-returns.edit', compact('supplierReturn', 'products', 'suppliers', 'purchases'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status === 'completed') {
            return redirect()->route('supplier-returns.index')
                ->with('error', 'Cannot update completed returns.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'quantity_returned' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $supplierReturn->update($request->all());

        return redirect()->route('supplier-returns.index')
            ->with('success', 'Supplier return updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierReturn $supplierReturn)
    {
        DB::beginTransaction();
        try {
            if ($supplierReturn->status === 'completed') {
                // Restore the quantity to the product
                $supplierReturn->product->increment('quantity', $supplierReturn->quantity_returned);

                // Restore the quantity to the specific purchase batch
                if ($supplierReturn->purchase_product_id) {
                    DB::table('purchase_products')
                        ->where('id', $supplierReturn->purchase_product_id)
                        ->increment('remaining_quantity', $supplierReturn->quantity_returned);
                }
            }

            $supplierReturn->delete();
            DB::commit();

            return redirect()->route('supplier-returns.index')
                ->with('success', 'تم حذف المرتجع وإعادة الكمية للمخزون بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('supplier-returns.index')
                ->with('error', 'حدث خطأ أثناء حذف المرتجع: ' . $e->getMessage());
        }
    }

    /**
     * Get products by supplier for AJAX requests
     */
    public function getProductsBySupplier($supplierId)
    {
        $products = Product::whereHas('purchases', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->with([
                    'purchases' => function ($query) use ($supplierId) {
                        $query->where('supplier_id', $supplierId);
                    }
                ])->get();

        return response()->json($products);
    }

    /**
     * Get purchases by supplier for AJAX requests
     */
    public function getPurchasesBySupplier($supplierId)
    {
        $purchases = Purchase::where('supplier_id', $supplierId)
            ->where('type', 'product')
            ->with('products')
            ->get();

        return response()->json($purchases);
    }

    /**
     * Get available stock batches for a product from a specific supplier
     */
    public function getStockBatches($productId, $supplierId)
    {
        $product = Product::findOrFail($productId);
        $stockBatches = $product->getStockFromSupplier($supplierId);

        $batchesData = $stockBatches->map(function ($batch) {
            $purchaseDate = $batch->created_at;
            if (is_numeric($purchaseDate)) {
                $purchaseDate = date('Y-m-d', $purchaseDate);
            } elseif ($purchaseDate instanceof \Carbon\Carbon) {
                $purchaseDate = $purchaseDate->format('Y-m-d');
            } else {
                $purchaseDate = date('Y-m-d', strtotime($purchaseDate));
            }

            return [
                'id' => $batch->id,
                'purchase_id' => $batch->purchase_id,
                'purchase_invoice' => $batch->purchase->invoice_number,
                'cost_price' => $batch->cost_price,
                'remaining_quantity' => $batch->remaining_quantity,
                'purchase_date' => $purchaseDate,
                'batch_info' => "Batch #{$batch->id} - {$batch->purchase->invoice_number} ({$batch->remaining_quantity} available)"
            ];
        });

        return response()->json($batchesData);
    }
}
