<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseProduct;
use App\Models\PurchaseInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\Type;
use Illuminate\Validation\Rule;
use Storage;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchasesController extends Controller
{
    public function export()
    {
        return Excel::download(new PurchasesExport, 'purchases_' . now()->format('Y-m-d_H-i') . '.xlsx');
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();  // Fetch all suppliers to display in the Select2 dropdown
        return view('admin.purchases.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'invoice_number' => 'required|string|unique:purchases,invoice_number',
            'type' => 'required|in:product,expense',
            'description' => 'nullable|string|max:255',
            'paid_amount' => 'required|numeric|min:0', // Paid amount will be stored as an installment
        ];
        // Validation messages
        $messages = [
            'invoice_number.required' => 'يرجى إدخال رقم الفاتورة.',
            'invoice_number.unique' => 'رقم الفاتورة هذا مستخدم من قبل.',
            'type.required' => 'يرجى اختيار نوع الفاتورة.',
            'type.in' => 'النوع المختار غير صالح.',
            'description.max' => 'الوصف لا يمكن أن يتجاوز 255 حرفاً.',
            'total_amount.required' => 'يرجى إدخال المبلغ الإجمالي عند اختيار النوع "نفقات".',
            'total_amount.numeric' => 'يجب أن يكون المبلغ الإجمالي رقماً.',
            'total_amount.min' => 'يجب أن يكون المبلغ الإجمالي أكبر من أو يساوي 0.',
        ];

        // Conditional validation for 'product' type
        if ($request->type == "product") {
            $rules['supplier_id'] = 'required|exists:suppliers,id';  // Supplier required for 'product'
        }

        // Conditional validation for 'expense' type
        if ($request->type == "expense") {
            $rules['total_amount'] = 'required|numeric|min:0'; // For expenses, total_amount is required
        }

        // Validate the request
        $validatedData = $request->validate($rules);

        try {
            $totalAmount = $request->type == 'expense' ? $validatedData['total_amount'] : 0;

            // Create the purchase record
            $purchase = Purchase::create([
                'invoice_number' => $validatedData['invoice_number'],
                'type' => $validatedData['type'],
                'description' => $validatedData['description'],
                'total_amount' => $totalAmount,
                'paid_amount' => 0,  // Initial paid amount is 0, installments will adjust this later
                'change' => -$totalAmount,  // Will be recalculated based on installments
                'supplier_id' => $request->type == 'product' ? $validatedData['supplier_id'] : null,  // Set supplier for product purchases
            ]);

            // Save the paid_amount as an installment
            PurchaseInstallment::create([
                'purchase_id' => $purchase->id,
                'amount_paid' => $validatedData['paid_amount'],
                'date_paid' => now(),
            ]);

            // Recalculate total paid and change
            $totalPaid = $purchase->installments()->sum('amount_paid');
            $purchase->update([
                'paid_amount' => $totalPaid,
                'change' => $purchase->total_amount - $totalPaid,
            ]);

            return redirect()->route('purchases.index')->with('success', 'تم إنشاء الفاتورة وإضافة الدفعة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }


    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        // Search by invoice number or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);
        $suppliers = Supplier::all();
        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('products');

        // Calculate sales data for each product with proper FIFO tracking
        foreach ($purchase->products as $product) {
            // Get the specific purchase_product relationship
            $purchaseProduct = DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->where('product_id', $product->id)
                ->first();

            if ($purchaseProduct) {
                // Calculate sold quantity from THIS specific purchase batch
                $soldQuantityFromThisBatch = DB::table('sales')
                    ->where('purchase_product_id', $purchaseProduct->id)
                    ->sum('quantity');

                // Calculate sales amount from THIS specific purchase batch
                $salesAmountFromThisBatch = DB::table('sales')
                    ->where('purchase_product_id', $purchaseProduct->id)
                    ->sum('total_price');

                // Calculate transferred quantity from THIS specific purchase batch
                $transferredQuantityFromThisBatch = DB::table('product_transfers')
                    ->where('product_id', $product->id)
                    ->where('old_purchase_id', $purchase->id)
                    ->sum('transferred_quantity');

                // Calculate remaining quantity for this specific purchase batch
                // Note: $purchaseProduct->quantity is already decremented when products are transferred in transferProduct()
                $remainingQuantity = $purchaseProduct->quantity - $soldQuantityFromThisBatch;


                // Check if this product has been transferred
                $hasTransfers = DB::table('product_transfers')
                    ->where('product_id', $product->id)
                    ->where('old_purchase_id', $purchase->id)
                    ->exists();

                // Add the calculated values to the product for use in the view
                $product->sold_from_this_batch = $soldQuantityFromThisBatch;
                $product->sales_amount_from_this_batch = $salesAmountFromThisBatch;
                $product->remaining_quantity = max($remainingQuantity, 0);
                $product->profit_from_this_batch = ($product->selling_price - $purchaseProduct->cost_price) * $soldQuantityFromThisBatch;
                $product->has_transfers = $hasTransfers;
                $product->original_purchase_quantity = $purchaseProduct->quantity;
                $product->transferred_quantity = $transferredQuantityFromThisBatch;
            }
        }

        return view('admin.purchases.show', compact('purchase'));
    }

    public function dailyPurchases()
    {
        // Set timezone to Cairo
        $today = Carbon::now('Africa/Cairo')->startOfDay();

        // Get the total purchases for today
        $totalPurchases = Purchase::where('created_at', '>=', $today)->sum('total_amount');

        // Get the list of purchases for today
        $purchases = Purchase::where('created_at', '>=', $today)->get();

        // Pass the total and the purchases to the view
        return view('admin.purchases.daily', compact('totalPurchases', 'purchases'));
    }


    // In PurchasesController.php

    public function transferProductForm($purchaseId, $productId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $product = Product::findOrFail($productId);

        // Get the specific purchase_product relationship
        $purchaseProduct = DB::table('purchase_products')
            ->where('purchase_id', $purchaseId)
            ->where('product_id', $productId)
            ->first();

        if (!$purchaseProduct) {
            return redirect()->back()->with('error', 'المنتج غير موجود في هذه الفاتورة.');
        }

        // Calculate sold quantity from THIS specific purchase batch
        $soldQuantityFromThisBatch = DB::table('sales')
            ->where('purchase_product_id', $purchaseProduct->id)
            ->sum('quantity');

        // Calculate remaining quantity for this specific purchase batch
        $remainingQuantity = $purchaseProduct->quantity - $soldQuantityFromThisBatch;

        // Fetch all purchases for the dropdown
        $purchases = Purchase::where('type', 'product')
            ->where('id', '!=', $purchase->id) // Exclude the selected purchase
            ->get();

        return view('admin.purchases.transfer', compact('purchase', 'product', 'remainingQuantity', 'purchases', 'soldQuantityFromThisBatch', 'purchaseProduct'));
    }


    public function transferProduct(Request $request, Purchase $purchase, Product $product)
    {
        // Validate the transfer request
        $validatedData = $request->validate([
            'new_purchase_id' => 'required|exists:purchases,id',
            'new_product_name' => 'required|string|max:255',
            'new_cost_price' => 'required|numeric|min:0',
            'new_selling_price' => 'required|numeric|min:0',
            'transfer_quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'new_purchase_id.required' => 'يرجى اختيار الفاتورة الجديدة.',
            'new_purchase_id.exists' => 'الفاتورة المحددة غير موجودة.',
            'new_product_name.required' => 'يرجى إدخال اسم المنتج الجديد.',
            'new_product_name.max' => 'اسم المنتج لا يمكن أن يتجاوز 255 حرفاً.',
            'new_cost_price.required' => 'يرجى إدخال سعر الشراء الجديد.',
            'new_cost_price.numeric' => 'سعر الشراء يجب أن يكون رقماً.',
            'new_cost_price.min' => 'سعر الشراء يجب أن يكون أكبر من أو يساوي 0.',
            'new_selling_price.required' => 'يرجى إدخال سعر البيع الجديد.',
            'new_selling_price.numeric' => 'سعر البيع يجب أن يكون رقماً.',
            'new_selling_price.min' => 'سعر البيع يجب أن يكون أكبر من أو يساوي 0.',
            'transfer_quantity.required' => 'يرجى إدخال كمية النقل.',
            'transfer_quantity.integer' => 'كمية النقل يجب أن تكون عدداً صحيحاً.',
            'transfer_quantity.min' => 'كمية النقل يجب أن تكون أكبر من أو تساوي 1.',
        ]);

        DB::beginTransaction();

        try {
            // Step 1: Get the specific purchase_product relationship
            $purchaseProduct = DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->where('product_id', $product->id)
                ->first();

            if (!$purchaseProduct) {
                throw new \Exception('المنتج غير موجود في هذه الفاتورة.');
            }

            // Calculate sold quantity from THIS specific purchase batch
            $soldQuantityFromThisBatch = DB::table('sales')
                ->where('purchase_product_id', $purchaseProduct->id)
                ->sum('quantity');

            // Calculate remaining quantity for this specific purchase batch
            $remainingQuantity = $purchaseProduct->quantity - $soldQuantityFromThisBatch;

            if ($remainingQuantity <= 0) {
                throw new \Exception('لا توجد كمية متبقية للنقل من هذه الفاتورة.');
            }

            if ($validatedData['transfer_quantity'] > $remainingQuantity) {
                throw new \Exception("كمية النقل ({$validatedData['transfer_quantity']}) أكبر من الكمية المتاحة ({$remainingQuantity}).");
            }

            // Step 2: Handle the image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
            } elseif ($product->image) {
                // Copy the original product image if no new image is uploaded
                $imagePath = $product->image;
            }

            // Step 3: Generate a unique barcode for the new product
            $barcodeString = strtoupper(uniqid('TRF-'));
            $barcodePath = 'barcodes/' . $barcodeString . '.svg';
            $barcodeSvg = Barcode::imageType('svg')
                ->foregroundColor('#000000')
                ->height(30)
                ->widthFactor(2)
                ->type(Type::TYPE_CODE_128)
                ->generate($barcodeString);

            Storage::disk('public')->put($barcodePath, $barcodeSvg);

            // Step 4: Create a new product for the transfer
            $newProduct = Product::create([
                'name' => $validatedData['new_product_name'],
                'cost_price' => $validatedData['new_cost_price'],
                'selling_price' => $validatedData['new_selling_price'],
                'color' => $product->color . '-منقول', // Add transfer indicator
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'quantity' => $validatedData['transfer_quantity'],
                'threshold' => $product->threshold,
                'image' => $imagePath,
                'barcode' => $barcodeString,
                'barcode_path' => $barcodePath,
            ]);

            // Step 5: Attach the new product to the target purchase
            $newPurchase = Purchase::find($validatedData['new_purchase_id']);
            $newPurchase->products()->attach($newProduct->id, [
                'quantity' => $validatedData['transfer_quantity'],
                'cost_price' => $validatedData['new_cost_price'],
                'remaining_quantity' => $validatedData['transfer_quantity'],
            ]);


            // Step 6: Update the original purchase_product batch
            $newRemainingQuantity = $remainingQuantity - $validatedData['transfer_quantity'];

            // Also update the main quantity field in purchase_products (not just remaining_quantity)
            DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->where('product_id', $product->id)
                ->decrement('quantity', $validatedData['transfer_quantity']);

            // Always keep the original product in the purchase for tracking purposes
            // Update the remaining quantity in the original purchase (can be 0)
            $purchase->products()->updateExistingPivot($product->id, [
                'remaining_quantity' => $newRemainingQuantity
            ]);
            $pivot = DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->where('product_id', $product->id)
                ->first();
            \Log::info('Updated remaining_quantity after transfer', [
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'new_remaining_quantity' => $pivot->remaining_quantity,
                'called_from' => 'PurchasesController@transferProduct',
            ]);

            // Step 7: Update the original product's total quantity
            $product->recalculateProductQuantity();

            // Step 8: Record the transfer in the product_transfers table
            DB::table('product_transfers')->insert([
                'old_purchase_id' => $purchase->id,
                'new_purchase_id' => $newPurchase->id,
                'product_id' => $product->id, // Original product
                'new_product_id' => $newProduct->id, // New transferred product
                'transferred_quantity' => $validatedData['transfer_quantity'],
                'quantity_before_transfer' => $remainingQuantity, // Store quantity before transfer
                'sold_quantity_old_purchase' => $soldQuantityFromThisBatch,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            // Step 9: Update total amounts for both purchases
            // Calculate total based on all items currently in the old purchase
            $oldPurchaseTotal = 0;
            foreach ($purchase->products as $p) {
                $pProd = DB::table('purchase_products')
                    ->where('purchase_id', $purchase->id)
                    ->where('product_id', $p->id)
                    ->first();

                if ($pProd) {
                    $oldPurchaseTotal += $pProd->quantity * $pProd->cost_price;
                }
            }


            $newPurchaseTotal = $newPurchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));

            $purchase->update([
                'total_amount' => $oldPurchaseTotal,
                'change' => $oldPurchaseTotal - $purchase->paid_amount
            ]);

            $newPurchase->update([
                'total_amount' => $newPurchaseTotal,
                'change' => $newPurchaseTotal - $newPurchase->paid_amount
            ]);

            // Step 10: Record quantity update for audit trail
            DB::table('quantity_updates')->insert([
                'product_id' => $newProduct->id,
                'new_quantity' => $validatedData['transfer_quantity'],
                'user_id' => auth()->id(),
                'action' => 'نقل من منتج آخر',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', "تم نقل المنتج بنجاح. تم إنشاء منتج جديد: {$newProduct->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'فشل في نقل المنتج: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function productTransfersReport()
    {
        $transfers = DB::table('product_transfers')
            ->join('products as old_products', 'product_transfers.product_id', '=', 'old_products.id')
            ->join('products as new_products', 'product_transfers.new_product_id', '=', 'new_products.id')
            ->join('purchases as old_purchases', 'product_transfers.old_purchase_id', '=', 'old_purchases.id')
            ->join('purchases as new_purchases', 'product_transfers.new_purchase_id', '=', 'new_purchases.id')
            ->select(
                'product_transfers.*',
                'old_products.name as old_product_name',
                'new_products.name as new_product_name',
                'old_products.cost_price as old_cost_price',
                'new_products.cost_price as new_cost_price',
                'old_products.selling_price as old_selling_price',
                'new_products.selling_price as new_selling_price',
                'old_purchases.invoice_number as old_invoice_number',
                'new_purchases.invoice_number as new_invoice_number',
                DB::raw("DATE_FORMAT(product_transfers.created_at, '%Y-%m-%d %H:%i') as formatted_created_at")
            )
            ->paginate(10); // Here you can specify the number of items per page

        return view('admin.reports.product_transfers', compact('transfers'));
    }

    public function getTransferHistory($purchaseId, $productId)
    {
        $transfers = DB::table('product_transfers')
            ->join('products as new_products', 'product_transfers.new_product_id', '=', 'new_products.id')
            ->join('purchases as new_purchases', 'product_transfers.new_purchase_id', '=', 'new_purchases.id')
            ->where('product_transfers.product_id', $productId)
            ->where('product_transfers.old_purchase_id', $purchaseId)
            ->select(
                'product_transfers.*',
                'new_products.name as new_product_name',
                'new_products.cost_price as new_cost_price',
                'new_products.selling_price as new_selling_price',
                'new_purchases.invoice_number as new_invoice_number',
                DB::raw("DATE_FORMAT(product_transfers.created_at, '%Y-%m-%d %H:%i') as formatted_created_at")
            )
            ->get();

        return response()->json($transfers);
    }

    public function destroy($id)
    {
        // Find the purchase by ID
        $purchase = Purchase::findOrFail($id);

        // Only allow deletion if the type is 'expense'
        if ($purchase->type != 'expense') {
            return redirect()->route('purchases.index')->with('error', 'لا يمكنك حذف الفاتورة لأنها ليست من نوع النفقات.');
        }

        try {
            // Delete the purchase
            $purchase->delete();

            return redirect()->route('purchases.index')->with('success', 'تم حذف الفاتورة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('purchases.index')->with('error', 'حدث خطأ أثناء حذف الفاتورة: ' . $e->getMessage());
        }
    }

    public function recalculatePurchaseTotal($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);

        // Calculate total based on all quantities currently in the purchase
        $newTotal = 0;
        foreach ($purchase->products as $product) {
            $purchaseProduct = DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->where('product_id', $product->id)
                ->first();

            if ($purchaseProduct) {
                $newTotal += $purchaseProduct->quantity * $purchaseProduct->cost_price;
            }
        }


        $purchase->update([
            'total_amount' => $newTotal,
            'change' => $newTotal - $purchase->paid_amount
        ]);

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'تم إعادة حساب إجمالي الفاتورة بنجاح.');
    }
}

