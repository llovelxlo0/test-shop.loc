<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Models\Category;


class CategoryController extends Controller
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('categories.index', compact('categories'));
    }
    public function create(Request $request)
    {
        $parents = $this->categoryService->getParentCategories();
        $selectedParentId = $request->query('parent_id');
        $childCategories = $selectedParentId ? $this->categoryService->getChildCategories($selectedParentId) : collect();

    return view('categories.create', compact('parents', 'selectedParentId', 'childCategories'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        $this->categoryService->createCategory($data);
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана.');
    }
    public function edit(Category $category, Request $request)
    {
    $parents = $this->categoryService->getParentCategories();

        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id', $category ? $category->parent_id : null);

        $childCategories = collect();
        if ($request->filled('parent_id')) {
            $childCategories = $this->categoryService->getChildCategories($request->parent_id);
        } elseif ($category && $category->parent_id) {
            $childCategories = $this->categoryService->getChildCategories($category->parent_id);
        }
        return view('categories.edit', compact('category', 'parents', 'childCategories', 'selectedParentId'));
    }
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        $this->categoryService->updateCategory($category, $data);
        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена.');
    }
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);
        return redirect()->route('categories.index')->with('success', 'Категория успешно удалена.');
    }
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}