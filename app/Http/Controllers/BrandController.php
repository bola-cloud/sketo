<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('name')->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string|max:1000',
        ]);

        Brand::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('brands.index')->with('success', 'تم إنشاء الماركة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $products = $brand->products()->paginate(20);
        return view('admin.brands.show', compact('brand', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $brand->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('brands.index')->with('success', 'تم تحديث الماركة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return redirect()->route('brands.index')->withErrors(['error' => 'لا يمكن حذف الماركة لأنها تحتوي على منتجات.']);
        }

        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'تم حذف الماركة بنجاح.');
    }
}
