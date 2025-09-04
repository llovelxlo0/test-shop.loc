<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;





class CategoryController extends Controller
{
    public function index() {
        
        $categories = Category::all();
        return view('categories.index', compact('categories')); // categories/index.blade.php
    }

    //Created Forms
    public function create() {

        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        return view('categories.create'); // categories/create.blade.php
    }

    //save new category
    public function store(Request $request) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('uploads', 'public');
    }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath
        ]);
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана!.');
    }

    //edit category
    public function edit(Category $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        return view('categories.edit', compact('category')); // categories/edit.blade.php
    }

    //update category
    public function update(Request $request, Category $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $category->update($request->only('name', 'description'));
        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена!.');
    }

    //delete category
    public function destroy(Category $category) {
        if (Auth::user()->usertype !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Доступ запрещен!.'); // if user Access denied
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Категория успешно удалена!.');
    }
}
