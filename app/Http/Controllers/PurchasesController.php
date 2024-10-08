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

class PurchasesController extends Controller
{
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();  // Fetch all suppliers to display in the Select2 dropdown
        return view('admin.purchases.create', compact('products','suppliers'));
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
                'change' => $totalPaid - $purchase->total_amount,
            ]);

            return redirect()->route('purchases.index')->with('success', 'تم إنشاء الفاتورة وإضافة الدفعة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }
    

    public function index()
    {
        $purchases = Purchase::with('supplier')->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('products');
        // dd($purchase);
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

        // Calculate remaining quantity
        $soldQuantity = $product->sales->sum('quantity'); // Total quantity sold
        $remainingQuantity = $purchase->products->find($productId)->pivot->quantity - $soldQuantity;

        // Fetch all purchases for the dropdown
        $purchases = Purchase::where('type', 'product')
        ->where('id', '!=', $purchase->id) // Exclude the selected purchase
        ->get();
    
        return view('admin.purchases.transfer', compact('purchase', 'product', 'remainingQuantity', 'purchases'));
    }


    public function transferProduct(Request $request, Purchase $purchase, Product $product)
    {
        $validatedData = $request->validate([
            'product_name' => 'string|unique:products,name',
            'new_purchase_id' => 'required|exists:purchases,id',
            'new_cost_price' => 'required|numeric|min:0',
            'new_selling_price' => 'required|numeric|min:0',
        ], [
            'product_name.string' => 'اسم المنتج يجب أن يكون نصاً.',
            'product_name.unique' => 'اسم المنتج موجود بالفعل. يرجى اختيار اسم آخر.',
            'new_purchase_id.required' => 'يرجى اختيار الفاتورة الجديدة.',
            'new_purchase_id.exists' => 'الفاتورة المحددة غير موجودة.',
            'new_cost_price.required' => 'يرجى إدخال سعر الشراء الجديد.',
            'new_cost_price.numeric' => 'سعر الشراء يجب أن يكون رقماً.',
            'new_cost_price.min' => 'سعر الشراء يجب أن يكون أكبر من أو يساوي 0.',
            'new_selling_price.required' => 'يرجى إدخال سعر البيع الجديد.',
            'new_selling_price.numeric' => 'سعر البيع يجب أن يكون رقماً.',
            'new_selling_price.min' => 'سعر البيع يجب أن يكون أكبر من أو يساوي 0.',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Step 1: Calculate the remaining quantity for the old product
            $totalSoldQuantity = $product->sales->sum('quantity');
            $remainingQuantity = $purchase->products()->where('product_id', $product->id)->first()->pivot->quantity - $totalSoldQuantity;
    
            // Step 2: Handle the image upload (if an image exists)
            $imagePath = $product->image ?? null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
            }
    
            // Step 3: Generate a unique barcode string and create the barcode image
            $barcodeString = strtoupper(uniqid());
            $barcodePath = 'barcodes/' . $barcodeString . '.svg';
            $barcodeSvg = Barcode::imageType('svg')
                ->foregroundColor('#000000')
                ->height(30)
                ->widthFactor(2)
                ->type(Type::TYPE_CODE_128)
                ->generate($barcodeString);
    
            Storage::disk('public')->put($barcodePath, $barcodeSvg);
    
            // Step 4: Create a new product with the remaining quantity, new prices, image, and barcode
            $newProduct = Product::create([
                'name' => $validatedData['product_name'],
                'cost_price' => $validatedData['new_cost_price'],
                'selling_price' => $validatedData['new_selling_price'],
                'color' => $product->color,
                'category_id' => $product->category_id,
                'quantity' => $remainingQuantity,
                'threshold' => $product->threshold,
                'image' => $imagePath,
                'barcode' => $barcodeString,
                'barcode_path' => $barcodePath,
            ]);
    
            // Step 5: Store the transfer information in the product_transfers table
            DB::table('product_transfers')->insert([
                'old_purchase_id' => $purchase->id,
                'new_purchase_id' => $validatedData['new_purchase_id'],
                'product_id' => $product->id,
                'transferred_quantity' => $remainingQuantity,
                'sold_quantity_old_purchase' => $totalSoldQuantity,
                'new_product_id' => $newProduct->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Step 6: Check if all the quantity is being transferred and no sold quantity
            if ($totalSoldQuantity == 0 && $remainingQuantity > 0) {
                // Delete the old product from the purchase
                $purchase->products()->detach($product->id);
    
                // Delete the product record if there are no sales
                $product->delete();
            } else {
                // Step 7: Update the old product's quantity in the products table to zero
                $product->update(['quantity' => 0]);
    
                // Step 8: Update the quantity in the purchase_products pivot table to reflect the sold quantity
                $purchase->products()->updateExistingPivot($product->id, ['quantity' => $totalSoldQuantity]);
            }
    
            // Step 9: Attach the new product to the new purchase
            $newPurchase = Purchase::find($validatedData['new_purchase_id']);
            $newPurchase->products()->attach($newProduct->id, [
                'quantity' => $remainingQuantity,
                'cost_price' => $validatedData['new_cost_price'],
            ]);
    
            // Step 10: Update the total amounts for the old and new purchases
            $oldPurchaseTotal = $purchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
            $newPurchaseTotal = $newPurchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
    
            // Update the total amounts
            $purchase->update(['total_amount' => $oldPurchaseTotal]);
            $newPurchase->update(['total_amount' => $newPurchaseTotal]);
    
            // Step 11: Recalculate the change for both the original and new purchases
            $purchase->update(['change' => $purchase->total_amount - $purchase->paid_amount]);
            $newPurchase->update(['change' => $newPurchase->total_amount - $newPurchase->paid_amount]);
    
            DB::commit();
    
            return redirect()->route('purchases.show', $purchase->id)->with('success', 'تم نقل الكمية المتبقية إلى الفاتورة الجديدة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء نقل الكمية: ' . $e->getMessage());
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
}

