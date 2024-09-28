<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'تم إضافة الفئة بنجاح.');
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'تم تحديث الفئة بنجاح.');
    }

    public function destroy(Category $category)
    {
        // Set category_id to null for all products related to the category
        $category->products()->update(['category_id' => null]);
        
        // Now delete the category
        $category->delete();
    
        return redirect()->route('categories.index')->with('success', 'تم حذف الفئة بنجاح.');
    }
    
}
