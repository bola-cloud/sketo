<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\Type;
use Storage;
class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filter by search term
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }
    
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    
        $products = $query->with('category')->paginate(20);
    
        $categories = Category::all(); // Assuming you have a Category model
    
        return view('admin.product.index', compact('products', 'categories'));
    }
    
    public function printSelectedBarcodes(Request $request)
    {
        $selectedProducts = Product::whereIn('id', $request->input('selected_products'))->get();

        // Return a view for printing barcodes
        return view('admin.product.print_barcodes', compact('selectedProducts'));
    }


    public function create()
    {
        // Retrieve all purchase invoices of type 'product'
        $purchases = Purchase::where('type', 'product')->get();
        $categories = Category::all(); // Assuming you have categories to be selected
        $products=Product::all();
        return view('admin.product.create', compact('purchases', 'categories','products'));
    }
    
    public function store(Request $request)
    {
        // Check if the user is adding a new product or an existing one
        if ($request->filled('existing_product')) {
            // Validation for existing product
            $validatedData = $request->validate([
                'purchase_id_existing' => 'required|exists:purchases,id',
                'quantity_existing' => 'required|integer|min:1',
            ]);
        } else {
            // Validation for new product
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'purchase_id' => 'required|exists:purchases,id',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'color' => 'required|string|max:255',
                'threshold' => 'required|integer|min:1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

    
        DB::beginTransaction();
    
        try {
            // Check if an existing product is being added to a new purchase
            if ($request->filled('existing_product')) {
                // Handle adding an existing product to a new purchase
                $product = Product::find($request->input('existing_product'));
                $purchase = Purchase::find($request->input('purchase_id_existing'));
            
                $quantity = $request->input('quantity_existing');
                $costPrice = $product->cost_price;
            
                // Attach the existing product to the new purchase
                $purchase->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                ]);
            
                // Update total amount of the purchase
                $totalAmount = $purchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
                $purchase->update(['total_amount' => $totalAmount]);
            
                // Recalculate the change for the purchase
                $change = $totalAmount - $purchase->paid_amount;
                $purchase->update(['change' => $change]);
            
                // Update the total quantity of the product across all purchases
                $totalQuantity = DB::table('purchase_products')
                    ->where('product_id', $product->id)
                    ->sum('quantity');
                
                // Update the product's total quantity
                $product->update(['quantity' => $totalQuantity]);
            
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
            $product = Product::create($validatedData);
    
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
    
            // Attach the new product to the purchase
            $purchase = Purchase::find($validatedData['purchase_id']);
            $purchase->products()->attach($product->id, [
                'quantity' => $validatedData['quantity'],
                'cost_price' => $validatedData['cost_price'],
            ]);
    
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
    
            DB::commit();
    
            return redirect()->route('products.index')->with('success', 'تم إنشاء المنتج وتوليد الباركود وتحديث إجمالي الفاتورة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.create')->with('error', 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage());
        }
    }
    
    public function edit(Product $product)
    {
        $product->load('purchases');
        
        // Retrieve all purchase invoices of type 'product' that are related to this product
        $purchases = $product->purchases()->where('type', 'product')->get();
        
        $categories = Category::all();
        
        // Calculate total quantity across all purchases
        $totalQuantity = $purchases->sum('pivot.quantity');
        
        return view('admin.product.edit', compact('product', 'categories', 'purchases', 'totalQuantity'));
    }
    

    public function update(Request $request, Product $product)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'color' => 'required|string|max:255',
            'threshold' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'يرجى إدخال اسم المنتج.',
            'name.max' => 'اسم المنتج لا يمكن أن يتجاوز 255 حرفاً.',
            'category_id.required' => 'يرجى اختيار الفئة.',
            'category_id.exists' => 'الفئة المختارة غير موجودة.',
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

            // Update product details (except for quantity)
            $product->update($validated);

            // Variables to store total quantities
            $totalQuantity = 0;

            // Loop over each purchase and update quantities
            foreach ($request->input('purchase_quantities') as $purchaseId => $newQuantity) {
                $purchase = Purchase::find($purchaseId);
                $oldQuantity = $purchase->products()->where('product_id', $product->id)->first()->pivot->quantity;

                // Update the pivot table with the new quantity
                $purchase->products()->updateExistingPivot($product->id, [
                    'quantity' => $newQuantity,
                ]);

                // Update total amount of the purchase (quantity * cost_price)
                $totalAmount = $purchase->products()->sum(DB::raw('purchase_products.quantity * purchase_products.cost_price'));
                $purchase->update(['total_amount' => $totalAmount]);

                // Recalculate the change (total_amount - paid_amount)
                $change = $totalAmount - $purchase->paid_amount;
                $purchase->update(['change' => $change]);

                // Accumulate total quantity for the product
                $totalQuantity += $newQuantity;

                // Log quantity updates if quantity has changed
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

            // Update the total quantity of the product in the products table
            $product->update(['quantity' => $totalQuantity]);

            DB::commit();
        
            return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.edit', $product)->with('error', 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        // Check if the product is related to any sales or invoices
        $hasSales = $product->sales()->exists();
        $hasPurchases = $product->purchases()->exists();

        if ($hasSales || $hasPurchases) {
            // If the product is related to sales or invoices, return a specific message
            return redirect()->route('products.index')->with('error', 'لا يمكن حذف المنتج لأنه مرتبط بمبيعات أو فواتير.');
        } else {
            // If no relations exist, delete the product
            DB::beginTransaction();

            try {
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

        return view('admin.product.print-barcode', compact('product', 'barcodePath'));
    }


    public function quantityUpdates()
    {
        $quantityUpdates = DB::table('quantity_updates')
            ->join('products', 'quantity_updates.product_id', '=', 'products.id')
            ->join('users', 'quantity_updates.user_id', '=', 'users.id')
            ->select('quantity_updates.*', 'products.name as product_name', 'users.name as user_name')
            ->get();

        return view('admin.quantity_updates.index', compact('quantityUpdates'));
    }

    public function printBarcodes($id)
    {
        // Retrieve the product by its ID
        $products = Product::where('id',$id)->get();
        // dd($products);

        return view('admin.product.print_barcodes', compact('products'));
    }

}
