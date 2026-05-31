<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sales;
use App\Models\PurchaseProduct;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\Type;
use Storage;
use Milon\Barcode\DNS1D;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function export()
    {
        return Excel::download(new ProductsExport, 'products_' . now()->format('Y-m-d_H-i') . '.xlsx');
    }

    public function index(Request $request)
    {
        $query = Product::query()->where('type', 'product');

        // Filter by search term
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->with(['category', 'brand'])->paginate(20);

        $categories = Category::all();
        $brands = Brand::all();

        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('admin') || $user->hasRole('owner') || $user->hasRole('super_admin'));
        $permissions = $user ? $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('name')->unique() : collect();

        return view('admin.product.index', compact('products', 'categories', 'brands', 'isAdmin', 'permissions'));
    }

    public function printSelectedBarcodes(Request $request)
    {
        $products = Product::whereIn('id', $request->input('selected_products'))->get();

        // Return a view for printing barcodes
        return view('admin.product.print_barcodes', compact('products'));
    }


    public function create()
    {
        // Retrieve all purchase invoices of type 'product'
        $purchases = Purchase::where('type', 'product')->get();
        $categories = Category::all(); // Assuming you have categories to be selected
        $brands = Brand::all(); // Get all brands
        $products = Product::where('type', 'product')->get();
        $suppliers = Supplier::all(); // Get all suppliers for the quick purchase modal
        return view('admin.product.create', compact('purchases', 'categories', 'brands', 'products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->merge(['is_weighted' => $request->has('is_weighted') ? 1 : 0]);
        // Check if the user is adding a new product or an existing one
        if ($request->filled('existing_product')) {
            // Validation for existing product
            $validatedData = $request->validate([
                'purchase_id_existing' => 'required|exists:purchases,id',
                'quantity_existing' => 'required|integer|min:1',
            ]);
        } else {
            // Validation for new product
            $isService = $request->input('type') === 'service';
            
            $rules = [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'type' => 'required|in:product,service',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'is_weighted' => 'nullable|boolean',
            ];

            if ($isService) {
                // If it's a service, ignore physical fields and auto-fill defaults
                $request->merge([
                    'purchase_id' => null,
                    'quantity' => 0,
                    'threshold' => 0,
                    'expiry_alert_days' => 0,
                    'color' => 'SRV-' . time() . rand(10, 99), // Unique auto-barcode for services
                ]);
            } else {
                $rules += [
                    'purchase_id' => 'required|exists:purchases,id',
                    'quantity' => 'required|numeric|min:0',
                    'color' => 'required|string|max:255',
                    'threshold' => 'required|numeric|min:0',
                    'expiry_date' => 'nullable|date',
                    'expiry_alert_days' => 'required|integer|min:0',
                    'sub_product_id' => 'nullable|exists:products,id',
                    'conversion_factor' => 'nullable|numeric|min:0.001',
                ];
            }

            $validatedData = $request->validate($rules);
        }

        $user = auth()->user();
        DB::beginTransaction();

        try {
            // Check if an existing product is being added to a new purchase
            if ($request->filled('existing_product')) {
                // Handle adding an existing product to a new purchase
                $product = Product::find($request->input('existing_product'));
                $purchase = Purchase::find($request->input('purchase_id_existing'));

                $quantity = $request->input('quantity_existing');
                $costPrice = $product->cost_price;

                // Check if the pivot already exists
                $existingPivot = DB::table('purchase_products')
                    ->where('purchase_id', $purchase->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($existingPivot && $existingPivot->quantity > 0) {
                    // Log before increment
                    $beforePivot = DB::table('purchase_products')->where('id', $existingPivot->id)->first();
                    \Log::info('Before incrementing purchase_products', [
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $beforePivot->quantity,
                        'remaining_quantity' => $beforePivot->remaining_quantity,
                        'called_from' => 'ProductController@store',
                    ]);
                    // Increment quantity and remaining_quantity in the existing batch
                    DB::table('purchase_products')
                        ->where('id', $existingPivot->id)
                        ->increment('quantity', $quantity);
                    DB::table('purchase_products')
                        ->where('id', $existingPivot->id)
                        ->increment('remaining_quantity', $quantity);
                    $newPivot = DB::table('purchase_products')->where('id', $existingPivot->id)->first();
                    \Log::info('Incremented remaining_quantity', [
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'added_quantity' => $quantity,
                        'new_quantity' => $newPivot->quantity,
                        'new_remaining_quantity' => $newPivot->remaining_quantity,
                        'called_from' => 'ProductController@store',
                    ]);
                } else {
                    // Log before creating new batch
                    \Log::info('Before creating new purchase_products row', [
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'added_quantity' => $quantity,
                        'called_from' => 'ProductController@store',
                    ]);
                    // Create a new batch (pivot row) for this product and purchase
                    $purchase->products()->attach($product->id, [
                        'vendor_id' => $user->vendor_id,
                        'quantity' => $quantity,
                        'cost_price' => $costPrice,
                        'remaining_quantity' => $quantity, // Add remaining_quantity
                    ]);
                    $newPivot = DB::table('purchase_products')
                        ->where('purchase_id', $purchase->id)
                        ->where('product_id', $product->id)
                        ->orderByDesc('id')->first();
                    \Log::info('Created new purchase_products row', [
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'added_quantity' => $quantity,
                        'new_quantity' => $newPivot->quantity,
                        'new_remaining_quantity' => $newPivot->remaining_quantity,
                        'called_from' => 'ProductController@store',
                    ]);
                }

                // Log all purchase_products for this product
                $allBatches = DB::table('purchase_products')
                    ->where('product_id', $product->id)
                    ->get();
                \Log::info('All purchase_products for product', [
                    'product_id' => $product->id,
                    'batches' => $allBatches,
                    'called_from' => 'ProductController@store',
                ]);
                // Recalculate and update the product's total quantity
                $product->recalculateProductQuantity();

                // Update total amount of the purchase
                $totalAmount = $purchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
                $purchase->update(['total_amount' => $totalAmount]);

                // Recalculate the change for the purchase
                $change = $totalAmount - $purchase->paid_amount;
                $purchase->update(['change' => $change]);

                // Calculate the product's total quantity based on all purchases minus sales minus transfers
                $totalPurchasedQuantity = DB::table('purchase_products')
                    ->where('product_id', $product->id)
                    ->sum('quantity');
                $totalSoldQuantity = DB::table('sales')
                    ->join('purchase_products', 'sales.purchase_product_id', '=', 'purchase_products.id')
                    ->where('purchase_products.product_id', $product->id)
                    ->sum('sales.quantity');
                $totalTransferredQuantity = DB::table('product_transfers')
                    ->where('product_id', $product->id)
                    ->sum('transferred_quantity');
                $availableQuantity = $totalPurchasedQuantity - $totalSoldQuantity - $totalTransferredQuantity;

                $product->recalculateProductQuantity();

                // Commit transaction and redirect
                DB::commit();

                return redirect()->route('products.index')->with('success', 'تمت إضافة المنتج إلى الفاتورة بنجاح.');
            }


            // Handle new product creation
            // Image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
                $validatedData['image'] = $imagePath;
            }

            // Create the new product
            $validatedData['is_weighted'] = $isService ? false : $request->has('is_weighted');
            
            if ($isService) {
                $validatedData['color'] = $request->input('color');
                $validatedData['quantity'] = 0;
                $validatedData['threshold'] = 0;
                $validatedData['expiry_alert_days'] = 0;
                $validatedData['is_weighted'] = false;
            }
            
            $product = Product::create($validatedData);

            // Handle Sub-units relationship if provided
            if ($request->filled('sub_product_id') && $request->filled('conversion_factor')) {
                \App\Models\ProductSubUnit::create([
                    'vendor_id' => auth()->user()->vendor_id,
                    'main_product_id' => $product->id,
                    'sub_product_id' => $request->sub_product_id,
                    'conversion_factor' => $request->conversion_factor
                ]);
            }

            // Generate the barcode string based on the 'color' field
            $barcodeString = $request->color;

            // Define the path for the barcode SVG
            $barcodePath = 'barcodes/' . $barcodeString . '.svg';

            // Generate the barcode image using the AgeekDev\Barcode package
            $barcodeSvg = Barcode::imageType('svg')
                ->foregroundColor('#000000')
                ->height(30)
                ->widthFactor(2)
                ->type(Type::TYPE_CODE_128) // Generate CODE 128 barcodes
                ->generate($barcodeString);

            // Save the barcode SVG
            Storage::disk('public')->put($barcodePath, $barcodeSvg);

            // Update the product with the barcode string and path
            $product->update(['barcode' => $barcodeString, 'barcode_path' => $barcodePath]);

            if (!$isService) {
                // Attach the new product to the purchase
                $purchase = Purchase::find($validatedData['purchase_id']);
                $purchase->products()->attach($product->id, [
                    'vendor_id' => auth()->user()->vendor_id,
                    'quantity' => $validatedData['quantity'],
                    'cost_price' => $validatedData['cost_price'],
                    'remaining_quantity' => $validatedData['quantity'], // Add remaining_quantity
                ]);

                // Recalculate and update the product's total quantity
                $product->recalculateProductQuantity();

                // Record the quantity update
                DB::table('quantity_updates')->insert([
                    'product_id' => $product->id,
                    'new_quantity' => $product->quantity,
                    'user_id' => auth()->id(),
                    'action' => 'إضافة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update total amount for the purchase
                $totalAmount = $purchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
                $purchase->update(['total_amount' => $totalAmount]);

                // Recalculate the change for the purchase
                $change = $totalAmount - $purchase->paid_amount;
                $purchase->update(['change' => $change]);
            }

            DB::commit();

            return redirect()->route('products.index')->with('success', 'تم إنشاء المنتج وتوليد الباركود وتحديث إجمالي الفاتورة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.create')->with('error', 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        if ($product->type === 'service') {
            abort(404);
        }
        $product->load('purchases');

        // Retrieve all purchase invoices of type 'product' that are related to this product
        $purchases = $product->purchases()->where('type', 'product')->get();

        $categories = Category::all();
        $brands = Brand::all(); // Get all brands

        // Calculate total quantity across all purchases
        $totalQuantity = $purchases->sum('pivot.quantity');

        $all_products = Product::where('id', '!=', $product->id)->where('type', 'product')->get();

        return view('admin.product.edit', compact('product', 'categories', 'brands', 'purchases', 'totalQuantity', 'all_products'));
    }


    public function update(Request $request, Product $product)
    {
        if ($product->type === 'service') {
            abort(404);
        }
        $request->merge(['is_weighted' => $request->has('is_weighted') ? 1 : 0]);
        // Uncomment for debugging
        // dd($request->all(), $product->toArray());

        $isService = false;
        
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_weighted' => 'nullable|boolean',
        ];

        if ($isService) {
            $request->merge([
                'threshold' => 0,
                'expiry_alert_days' => 0,
            ]);
        } else {
            $rules += [
                'color' => 'required|string|max:255',
                'threshold' => 'required|numeric|min:0',
                'expiry_date' => 'nullable|date',
                'expiry_alert_days' => 'required|integer|min:0',
                'sub_product_id' => 'nullable|exists:products,id',
                'conversion_factor' => 'nullable|numeric|min:0.001',
            ];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'يرجى إدخال اسم المنتج.',
            'name.max' => 'اسم المنتج لا يمكن أن يتجاوز 255 حرفاً.',
            'category_id.required' => 'يرجى اختيار الفئة.',
            'category_id.exists' => 'الفئة المختارة غير موجودة.',
            'brand_id.exists' => 'الماركة المختارة غير موجودة.',
            'purchase_id.required' => 'يرجى اختيار فاتورة الشراء.',
            'purchase_id.exists' => 'فاتورة الشراء المختارة غير موجودة.',
            'cost_price.required' => 'يرجى إدخال سعر التكلفة.',
            'cost_price.numeric' => 'سعر التكلفة يجب أن يكون رقماً.',
            'cost_price.min' => 'سعر التكلفة يجب أن يكون أكبر من أو يساوي 0.',
            'selling_price.required' => 'يرجى إدخال سعر البيع.',
            'selling_price.numeric' => 'سعر البيع يجب أن يكون رقماً.',
            'selling_price.min' => 'سعر البيع يجب أن يكون أكبر من أو يساوي 0.',
            'quantity.required' => 'يرجى إدخال الكمية.',
            'quantity.integer' => 'الكمية يجب أن تكون عدداً صحيحاً.',
            'quantity.min' => 'الكمية يجب أن تكون أكبر من أو تساوي 1.',

            'threshold.required' => 'يرجى إدخال الحد الأدنى للكمية.',
            'threshold.integer' => 'الحد الأدنى للكمية يجب أن يكون عدداً صحيحاً.',
            'threshold.min' => 'الحد الأدنى للكمية يجب أن يكون أكبر من أو يساوي 1.',
        ]);
        $user = auth()->user();
        DB::beginTransaction();

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                if ($product->image) {
                    \Storage::disk('public')->delete($product->image);
                }

                $imagePath = $request->file('image')->store('product_images', 'public');
                $validated['image'] = $imagePath;
            }

            // Check if barcode (color field) is changed
            if ($request->color !== $product->color) {
                // Generate a new barcode string based on the new 'color' field
                $barcodeString = $request->color;

                // Define the path for the new barcode SVG
                $barcodePath = 'barcodes/' . $barcodeString . '.svg';

                // Generate the new barcode image using the AgeekDev\Barcode package
                $barcodeSvg = Barcode::imageType('svg')
                    ->foregroundColor('#000000')
                    ->height(30)
                    ->widthFactor(2)
                    ->type(Type::TYPE_CODE_128) // Generate CODE 128 barcodes
                    ->generate($barcodeString);

                // Save the new barcode SVG
                Storage::disk('public')->put($barcodePath, $barcodeSvg);

                // Update the product with the new barcode string and path
                $product->update(['barcode' => $barcodeString, 'barcode_path' => $barcodePath]);
            }

            // Update the remaining product details (excluding quantity)
            $validated['is_weighted'] = $isService ? false : $request->has('is_weighted');
            $product->update($validated);

            // Variables to store total quantities
            $totalPurchasedQuantity = 0;
            $purchaseQuantitiesUpdated = false;

            // Check if purchase_quantities is provided and not empty
            if ($request->has('purchase_quantities') && is_array($request->input('purchase_quantities')) && !empty($request->input('purchase_quantities'))) {
                // Loop through each purchase_product entry using the pivot table's `id`
                foreach ($request->input('purchase_quantities') as $purchaseProductId => $newQuantity) {
                    $purchaseProduct = DB::table('purchase_products')->where('id', $purchaseProductId)->first();

                    if ($purchaseProduct) {
                        $oldQuantity = $purchaseProduct->quantity;

                        // Update the specific pivot row by its ID
                        DB::table('purchase_products')
                            ->where('id', $purchaseProductId)
                            ->update(['quantity' => $newQuantity]);

                        // Recalculate total amount and change for the related purchase
                        $purchase = Purchase::find($purchaseProduct->purchase_id);
                        $totalAmount = DB::table('purchase_products')
                            ->where('purchase_id', $purchase->id)
                            ->sum(DB::raw('quantity * cost_price'));
                        $purchase->update(['total_amount' => $totalAmount]);

                        $change = $totalAmount - $purchase->paid_amount;
                        $purchase->update(['change' => $change]);

                        // Accumulate the total purchased quantity
                        $totalPurchasedQuantity += $newQuantity;
                        $purchaseQuantitiesUpdated = true;

                        // Log changes if the quantity was modified
                        if ($oldQuantity != $newQuantity) {
                            DB::table('quantity_updates')->insert([
                                'product_id' => $product->id,
                                'old_quantity' => $oldQuantity,
                                'new_quantity' => $newQuantity,
                                'user_id' => auth()->id(),
                                'action' => 'تحديث',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Only update the product quantity if purchase quantities were actually updated
                // Calculate the total sold quantity
                $totalSoldQuantity = Sales::where('product_id', $product->id)->sum('quantity');

                // Update the total quantity in the products table
                $updatedQuantity = $totalPurchasedQuantity - $totalSoldQuantity;
                $product->update(['quantity' => max($updatedQuantity, 0)]); // Ensure quantity doesn't go below zero
            }

            // Update or Create Sub-units relationship
            if ($request->has('sub_product_id') && $request->filled('conversion_factor')) {
                \App\Models\ProductSubUnit::updateOrCreate(
                    ['main_product_id' => $product->id],
                    [
                        'vendor_id' => auth()->user()->vendor_id,
                        'sub_product_id' => $request->sub_product_id,
                        'conversion_factor' => $request->conversion_factor
                    ]
                );
            } elseif ($request->has('has_sub_units') && !$request->has('sub_product_id')) {
                // If the user unchecked or cleared it, we might want to delete it, 
                // but usually better to just updateOrCreate if data is present.
            }

            DB::commit();

            return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.edit', $product)->with('error', 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        if ($product->type === 'service') {
            abort(404);
        }
        // Check if the product has any sales
        $hasSales = $product->sales()->exists();

        if ($hasSales) {
            // If the product is related to sales, return a specific message
            return redirect()->route('products.index')->with('error', 'لا يمكن حذف المنتج لأنه مرتبط بمبيعات.');
        } else {
            // If no sales exist, delete the product
            DB::beginTransaction();

            try {
                // Delete related purchase_products dependencies first if any
                DB::table('purchase_products')->where('product_id', $product->id)->delete();

                // Delete the product
                $product->delete();

                DB::commit();

                return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('products.index')->with('error', 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage());
            }
        }
    }

    public function show(Product $product)
    {
        // Load the necessary relationships if needed, e.g., category, purchases, etc.
        return view('admin.product.search', compact('product'));
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->with('category')
            ->get();

        foreach ($products as $product) {
            $product->barcode_image = DNS1D::getBarcodePNGPath($product->barcode, 'C128');
        }

        return response()->json($products);
    }


    public function findProductByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json([
                'name' => $product->name,
                'category' => $product->category->name,
                'cost_price' => $product->cost_price,
                'selling_price' => $product->selling_price,
                'quantity' => $product->quantity,
                'barcode' => $product->barcode,
                'color' => $product->color,
                'barcode_image' => DNS1D::getBarcodePNGPath($product->barcode, 'C128'),
            ]);
        } else {
            return response()->json(['error' => 'لم يتم العثور على المنتج.'], 404);
        }
    }

    public function printBarcode(Product $product)
    {
        // استدعاء كلاس DNS1D
        $barcodeGenerator = new DNS1D();

        // توليد صورة الباركود كسلسلة base64
        $barcodeImage = $barcodeGenerator->getBarcodePNG($product->barcode, 'C128');

        // Create a file path for the barcode image in the 'barcodes' folder within 'public'
        $barcodePath = 'barcodes/' . $product->barcode . '.png';

        // Save the barcode image to the specified path
        \Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));

        $products = collect([$product]);
        return view('admin.product.print-barcode', compact('product', 'products', 'barcodePath'));
    }


    public function quantityUpdates()
    {
        $quantityUpdates = DB::table('quantity_updates')
            ->join('products', 'quantity_updates.product_id', '=', 'products.id')
            ->join('users', 'quantity_updates.user_id', '=', 'users.id')
            ->where('products.type', '!=', 'service')
            ->select('quantity_updates.*', 'products.name as product_name', 'users.name as user_name')
            ->get();

        // Get all products for the select2 dropdown
        $products = Product::where('type', 'product')->get();

        return view('admin.quantity_updates.index', compact('quantityUpdates', 'products'));
    }

    public function productTransactions()
    {
        // Fetch added quantities (purchases) from Purchase & QuantityUpdates
        $addedQuantities = DB::table('quantity_updates')
            ->join('products', 'quantity_updates.product_id', '=', 'products.id')
            ->join('users', 'quantity_updates.user_id', '=', 'users.id')
            ->leftJoin('purchase_products', 'quantity_updates.product_id', '=', 'purchase_products.product_id')
            ->leftJoin('purchases', 'purchase_products.purchase_id', '=', 'purchases.id')
            ->where('products.type', '!=', 'service')
            ->select(
                'quantity_updates.product_id',
                'products.name as product_name',
                'quantity_updates.old_quantity',
                'quantity_updates.new_quantity',
                'quantity_updates.action',
                'users.name as user_name',
                'purchases.invoice_number as purchase_invoice',
                'quantity_updates.created_at'
            )
            ->get();

        // Fetch sold quantities from Sales, including invoice details
        $soldQuantities = DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('invoices', 'sales.invoice_id', '=', 'invoices.id')
            ->join('users', 'invoices.user_id', '=', 'users.id')
            ->where('products.type', '!=', 'service')
            ->select(
                'sales.product_id',
                'products.name as product_name',
                'sales.quantity as sold_quantity',
                'invoices.invoice_code',
                'users.name as user_name',
                'invoices.created_at'
            )
            ->get();

        // Fetch all products for filtering
        $products = Product::where('type', 'product')->get();

        return view('admin.product.transactions', compact('addedQuantities', 'soldQuantities', 'products'));
    }


    public function printBarcodes($id)
    {
        $product = Product::findOrFail($id);
        $products = collect([$product]);
        return view('admin.product.print_barcodes', compact('product', 'products'));
    }

    public function recalculateAllProductQuantities()
    {
        $products = Product::where('type', 'product')->get();
        $updatedCount = 0;

        foreach ($products as $product) {
            // Calculate the product's total quantity based on all purchases minus sales minus transfers
            $totalPurchasedQuantity = DB::table('purchase_products')
                ->where('product_id', $product->id)
                ->sum('quantity');
            $totalSoldQuantity = DB::table('sales')
                ->join('purchase_products', 'sales.purchase_product_id', '=', 'purchase_products.id')
                ->where('purchase_products.product_id', $product->id)
                ->sum('sales.quantity');
            $totalTransferredQuantity = DB::table('product_transfers')
                ->where('product_id', $product->id)
                ->sum('transferred_quantity');
            $availableQuantity = $totalPurchasedQuantity - $totalSoldQuantity - $totalTransferredQuantity;

            // Update the product's total quantity
            $product->update(['quantity' => max($availableQuantity, 0)]);
            $updatedCount++;
        }

        return redirect()->route('products.index')
            ->with('success', "تم إعادة حساب كميات {$updatedCount} منتج بنجاح.");
    }

}
