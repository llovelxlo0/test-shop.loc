<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware('auth')->except(['index', 'show', 'getSubcategories']);
    }
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('categories.index', compact('categories'));
    }
    public function create(Request $request)
    {
        $this->authorize('create', Category::class);
        $parents = $this->categoryService->getParentCategories();
        $selectedParentId = $request->query('parent_id');
        $childCategories = $selectedParentId ? $this->categoryService->getChildCategories($selectedParentId) : collect();

    return view('categories.create', compact('parents', 'selectedParentId', 'childCategories'));
    }
    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        $data = $request->validated();
        $this->categoryService->createCategory($data);
        return redirect()->route('categories.index')->with('success', 'Категория успешно создана.');
    }
    public function edit(Category $category, Request $request)
    {
        $this->authorize('update', $category);
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
        $this->authorize('update', $category);
        $data = $request->validated();
        $this->categoryService->updateCategory($category, $data);
        return redirect()->route('categories.index')->with('success', 'Категория успешно обновлена.');
    }
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $this->categoryService->deleteCategory($category);
        return redirect()->route('categories.index')->with('success', 'Категория успешно удалена.');
    }
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
    public function getSubcategories(Category $parent)
    {
        return response()->json(
            $this->categoryService->getChildCategories($parent->id)
        );
    }
}
