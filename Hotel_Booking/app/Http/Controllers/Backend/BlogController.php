<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function BlogCategory()
    {
        $category = BlogCategory::latest()->get();
        return view('backend.category.blog_category', compact('category'));
    }

    public function storeBlogCategory(Request $request)
    {
        // Validation
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        BlogCategory::create([
            'category_name' => $request->category_name,
            'category_slug' => Str::slug($request->category_name),
        ]);

        $notification = [
            'message' => 'Blog Category Added Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    public function EditBlogCategory($id)
    {
      $categories = BlogCategory::find($id);
      return response()->json($categories);
    }
}
