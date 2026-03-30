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
use Auth;

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

    public function AddBlogPost() 
    {
        $blogcat = BlogCategory::latest()->get();
        return view('backend.post.add_post',compact('blogcat'));
    }


    
    public function StoreBlogPost(Request $request)
{
    // Validation des champs
    $request->validate([
        'blogcat_id' => 'required|exists:blog_categories,id',
        'post_title' => 'required|string|max:255',
        'short_descp' => 'required|string',
        'long_descp' => 'required|string',
        'post_image' => 'required|image|mimes:jpg,jpeg,png,gif,webp',
    ]);

    // Upload image simple
    if ($request->hasFile('post_image')) {
        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $uploadPath = 'upload/post/';

        // Créer le dossier si inexistant
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }

        // Déplacer l'image dans le dossier
        $image->move(public_path($uploadPath), $name_gen);

        $save_path = $uploadPath . $name_gen;
    } else {
        $save_path = null; // ou mettre une image par défaut si tu veux
    }

    // Insertion du post
    \App\Models\BlogPost::create([
        'blogcat_id' => $request->blogcat_id,
        'user_id'    => \Auth::user()->id,
        'post_title' => $request->post_title,
        'post_slug'  => \Illuminate\Support\Str::slug($request->post_title),
        'short_descp'=> $request->short_descp,
        'long_descp' => $request->long_descp,
        'post_image' => $save_path,
    ]);

    // Notification
    $notification = [
        'message' => 'Blog Post Data Inserted Successfully',
        'alert-type' => 'success'
    ];

    return redirect()->route('all.blog.post')->with($notification);
}

    
}
