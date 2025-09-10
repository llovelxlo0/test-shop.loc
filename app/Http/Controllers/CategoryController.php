<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Http\Requests\CategoryRequest;
use App\Models\CategoryType;
use App\Models\CategoryName;





class CategoryController extends Controller
{
    public function index() {
        
        $categories = Goods::all();
        $categoryTypes = CategoryType::all();
        $categoryNames = CategoryName::all();
        return view('categories.index', compact('categories' , 'categoryTypes' , 'categoryNames')); // categories/index.blade.php
    }

    //Created Forms
    public function create() {

        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        $categoryTypes = CategoryType::all();
        $categoryNames = CategoryName::all();

        return view('categories.create' , compact('categoryTypes' , 'categoryNames')); // categories/create.blade.php
    }

    //save new category
    public function store(Request $request) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }

        $request->validate([
            'category_type_id' => 'required|exists:category_types,id',
            'category_name_id' => 'required|exists:category_names,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('uploads', 'public');
    }

        Goods::create([
            'category_type_id' => $request->category_type_id,
            'category_name_id' => $request->category_name_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath
        ]);
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана!.');
    }

    //edit category
    public function edit(Goods $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        $categoryTypes = CategoryType::all();
        $categoryNames = CategoryName::all();

        return view('categories.edit', compact('category', 'categoryType' , 'categoryNames')); // categories/edit.blade.php
    }

    //update category
    public function update(Request $request, Goods $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }

        $request->validate([
            'category_type' => 'nullable|string|max:255',
            'category_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $category->update($request->only('name', 'description'));
        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена!.');
    }

    //delete category
    public function destroy(Goods $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Категория успешно удалена!.');
    }
}
