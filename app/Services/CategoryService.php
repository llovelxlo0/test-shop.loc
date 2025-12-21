<?php
namespace App\Services;
use App\Models\Category;
class CategoryService
{
    public function getParentCategories()
{
    return Category::whereNull('parent_id')->pluck('name', 'id');
}

    public function getChildCategories($parentId)
    {
        return Category::where('parent_id', $parentId)->select('id', 'name')->get();
    }
    public function getAllCategories()
    {
        return Category::with('parent')->get();
    }

    public function createCategory(array $data)
    {
        if (empty($data['parent_id'])) {
        $data['parent_id'] = null;
        }

    return Category::create($data);
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
