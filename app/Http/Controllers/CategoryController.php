<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index() {
        
        $categories = Goods::with('category')->paginate(15);
        return view('categories.index', compact('categories' )); // categories/index.blade.php
    }

    //Created Forms
    public function create(Request $request) {
        $parents = Category::whereNull('parent_id')->pluck('name', 'id');

        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id');

        $childCategories = collect();
        if ($request->filled('parent_id')) {
            $childCategories = Category::where('parent_id', $request->parent_id)->pluck('name', 'id');
        }

    return view('categories.create', compact('parents', 'childCategories' , 'selectedParentId')); // categories/create.blade.php
    }

    //save new category
    public function store(CategoryRequest $request) {
        
        $imagePath = null;
        if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('uploads', 'public');
    }

        Goods::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath
        ]);
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана!.');
    }

    //edit category
    public function edit(Request $request , Goods $category) {
       $parents = Category::whereNull('parent_id')->pluck('name', 'id');

       $selectedParentId = $request->get('parent_id') ?? optional($category->category)->parent_id ?? null;

       $childCategories = collect();
         if ($selectedParentId) {
                $childCategories = Category::where('parent_id', $selectedParentId)->pluck('name', 'id');
         }

    
    return view('categories.edit', compact('category', 'parents' , 'childCategories', 'selectedParentId')); // categories/edit.blade.php
    }

    //update category
    public function update(CategoryRequest $request, Goods $category) {
        //dd('reached update', $request->all(), $category);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $category->image = $imagePath;
        }

        $category->category_id = $request->category_id;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->price = $request->price;
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена!.');
    }

    //delete category
    public function destroy(Goods $category) {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Категория успешно удалена!.');
    }
}
