<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\Type;
use Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('type', 'service');

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

        $services = $query->with(['category'])->paginate(20);

        $categories = Category::all();

        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('admin') || $user->hasRole('owner') || $user->hasRole('super_admin'));
        $permissions = $user ? $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('name')->unique() : collect();

        return view('admin.services.index', compact('services', 'categories', 'isAdmin', 'permissions'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'color' => 'nullable|string|max:255|unique:products,barcode', // Custom barcode
        ], [
            'name.required' => __('app.products.validation_error') . ' ' . __('app.products.name_required'),
            'category_id.required' => __('app.products.validation_error') . ' ' . __('app.products.category_required'),
        ]);

        DB::beginTransaction();
        try {
            // Generate barcode if not provided
            $barcodeString = $request->input('color');
            if (empty($barcodeString)) {
                $barcodeString = 'SRV-' . time() . rand(10, 99);
            }

            // Path for barcode SVG
            $barcodePath = 'barcodes/' . $barcodeString . '.svg';

            // Generate SVG barcode
            $barcodeSvg = Barcode::imageType('svg')
                ->foregroundColor('#000000')
                ->height(30)
                ->widthFactor(2)
                ->type(Type::TYPE_CODE_128)
                ->generate($barcodeString);

            Storage::disk('public')->put($barcodePath, $barcodeSvg);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
            }

            $service = Product::create([
                'type' => 'service',
                'name' => $request->name,
                'category_id' => $request->category_id,
                'brand_id' => null,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
                'quantity' => 0,
                'barcode' => $barcodeString,
                'barcode_path' => $barcodePath,
                'color' => $barcodeString,
                'threshold' => 0,
                'image' => $imagePath,
                'is_weighted' => false,
                'expiry_alert_days' => 0,
            ]);

            DB::commit();
            return redirect()->route('services.index')->with('success', __('app.products.add_success') ?? 'تم إضافة الخدمة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('services.create')->with('error', __('app.products.unexpected_error') . ' ' . $e->getMessage());
        }
    }

    public function edit(Product $service)
    {
        if ($service->type !== 'service') {
            abort(404);
        }
        $categories = Category::all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Product $service)
    {
        if ($service->type !== 'service') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'color' => 'required|string|max:255|unique:products,barcode,' . $service->id,
        ], [
            'name.required' => __('app.products.validation_error') . ' ' . __('app.products.name_required'),
            'category_id.required' => __('app.products.validation_error') . ' ' . __('app.products.category_required'),
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                $imagePath = $request->file('image')->store('product_images', 'public');
                $validated['image'] = $imagePath;
            }

            // Check barcode changes
            if ($request->color !== $service->color) {
                $barcodeString = $request->color;
                $barcodePath = 'barcodes/' . $barcodeString . '.svg';

                $barcodeSvg = Barcode::imageType('svg')
                    ->foregroundColor('#000000')
                    ->height(30)
                    ->widthFactor(2)
                    ->type(Type::TYPE_CODE_128)
                    ->generate($barcodeString);

                Storage::disk('public')->put($barcodePath, $barcodeSvg);
                $service->update([
                    'barcode' => $barcodeString,
                    'barcode_path' => $barcodePath,
                    'color' => $barcodeString
                ]);
            }

            $service->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'brand_id' => null,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
                'image' => $validated['image'] ?? $service->image,
            ]);

            DB::commit();
            return redirect()->route('services.index')->with('success', __('app.products.update_success') ?? 'تم تحديث الخدمة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('services.edit', $service->id)->with('error', __('app.products.unexpected_error') . ' ' . $e->getMessage());
        }
    }

    public function destroy(Product $service)
    {
        if ($service->type !== 'service') {
            abort(404);
        }

        try {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            if ($service->barcode_path) {
                Storage::disk('public')->delete($service->barcode_path);
            }
            $service->delete();
            return redirect()->route('services.index')->with('success', __('app.products.delete_success') ?? 'تم حذف الخدمة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('services.index')->with('error', __('app.products.unexpected_error') . ' ' . $e->getMessage());
        }
    }
}
