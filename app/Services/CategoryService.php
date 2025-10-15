<?php
namespace App\Services;
use App\Models\Category;
class CategoryService
{
    public function getParentCategories()
{
    return Category::whereNull('parent_id')->whereHas('children')->pluck('name', 'id');
}

    public function getChildCategories($parentId)
    {
        return Category::where('parent_id', $parentId)->pluck('name', 'id');
    }
    public function getAllCategories()
    {
        return Category::with('parent')->get();
    }

    public function createCategory(array $data)
    {
        return Category::create([
            'name' => $data['name'],
            'parent_id' =>$data['parent_id'] ?? null
        ]);
    }
    public function updateCategory(Category $category, array $data)
    {
        $category->name = $data['name'];
        $category->parent_id = $data['parent_id'] ?? null;
        $category->save();

        return $category;
    }
    public function deleteCategory(Category $category)
    {
        // Дополнительная логика проверки перед удалением, если необходимо
        return $category->delete();
    }
}