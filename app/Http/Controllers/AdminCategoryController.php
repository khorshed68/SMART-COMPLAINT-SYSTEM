<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCategoryController extends Controller
{
    /**
     * Category management view.
     */
    public function index()
    {
        return view('admin.categories');
    }

    /**
     * AJAX: Get all categories with complaint counts.
     */
    public function getCategories()
    {
        $categories = Category::withComplaintCount()->get();
        
        // Append complaint_count accessor attribute to array output
        $data = $categories->map(function ($cat) {
            $arr = $cat->toArray();
            $arr['complaint_count'] = $cat->complaint_count;
            return $arr;
        });

        return response()->json($data);
    }

    /**
     * AJAX: Create a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon ?? 'fa-ellipsis-h',
            'color' => $request->color ?? '#3498db',
        ]);

        AuditService::log(
            Auth::id(),
            'create_category',
            'Category',
            $category->id,
            null,
            $category->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category
        ]);
    }

    /**
     * AJAX: Update category details.
     */
    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => "required|string|max:50|unique:categories,name,{$id}",
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $oldValues = $category->toArray();

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon ?? $category->icon,
            'color' => $request->color ?? $category->color,
        ]);

        AuditService::log(
            Auth::id(),
            'update_category',
            'Category',
            $id,
            $oldValues,
            $category->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'category' => $category
        ]);
    }

    /**
     * AJAX: Delete category (blocked if complaints exist).
     */
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        if ($category->complaints()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category. It is referenced by existing complaints.'
            ], 422);
        }

        $oldValues = $category->toArray();
        $category->delete();

        AuditService::log(
            Auth::id(),
            'delete_category',
            'Category',
            $id,
            $oldValues,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }
}
