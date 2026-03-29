<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function BlogCategory()
    {
        $category = BlogCategory::latest()->get();
        return view('backend.category.blog_category', compact('category'));
    }

    public function StoreBlogCategory(Request $request)
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

    
    
    public function UpdateBlogCategory(Request $request)
    {
       // Validation
       $request->validate([
           'cat_id' => 'required|exists:blog_categories,id',
           'category_name' => 'required|string|max:255',
       ]);

       // Récupérer la catégorie ou renvoyer 404 si introuvable
       $category = BlogCategory::findOrFail($request->cat_id);

       // Mettre à jour le nom et le slug
       $category->update([
           'category_name' => $request->category_name,
           'category_slug' => Str::slug($request->category_name),
       ]);

       // Notification
       $notification = [
           'message' => 'Blog Category Updated Successfully',
           'alert-type' => 'success'
       ];

       return redirect()->back()->with($notification);
    }

    public function DeleteBlogCategory($id)
    {
       // Récupérer la catégorie ou renvoyer 404 si introuvable
       $category = BlogCategory::findOrFail($id);

       // Supprimer la catégorie
       $category->delete();

       // Notification
       $notification = [
           'message' => 'Blog Category Deleted Successfully',
           'alert-type' => 'success'
       ];

       return redirect()->back()->with($notification);
    }

    /////////////////////////// All Blog Post Methods /////////////////////////////

    public function AllBlogPost()
    {
        $post = BlogPost::latest()->get();
        return view('backend.post.all_post', compact('post'));
    }
}
