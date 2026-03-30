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
use Intervention\Image\Facades\Image;
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

  // Edit blog post
    public function EditBlogPost($id)
    {
        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::findOrFail($id); // sécurité si ID inexistant
        return view('backend.post.edit_post', compact('blogcat', 'post'));
    }


    // Update blog post
 
   public function UpdateBlogPost(Request $request)
   {
    $post_id = $request->id;

     $data = [
        'blogcat_id' => $request->blogcat_id,
        'user_id' => Auth::user()->id,
        'post_title' => $request->post_title, 
        'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
        'short_descp' => $request->short_descp,
        'long_descp' => $request->long_descp,
        'created_at' => Carbon::now(),
     ];

      // Gestion de l'image si upload
     if ($request->hasFile('post_image')) {
        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $uploadPath = 'upload/post';

        // Crée le dossier s'il n'existe pas
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }

        // Déplace l'image
        $image->move(public_path($uploadPath), $name_gen);
        $data['post_image'] = $uploadPath . '/' . $name_gen;

        // Supprime l'ancienne image si elle existe
        $oldPost = BlogPost::findOrFail($post_id);
        if ($oldPost->post_image && file_exists(public_path($oldPost->post_image))) {
            unlink(public_path($oldPost->post_image));
        }
      }

      // Mettre à jour le post
      BlogPost::findOrFail($post_id)->update($data);

      $notification = [
        'message' => 'BlogPost Updated Successfully',
        'alert-type' => 'success'
      ];

      return redirect()->route('all.blog.post')->with($notification);
}


    // Delete blog post
    public function DeleteBlogPost($id)
    {
        $item = BlogPost::findOrFail($id);

        // Supprimer l'image si elle existe
        if ($item->post_image && file_exists(public_path($item->post_image))) {
            unlink(public_path($item->post_image));
        }

        // Supprimer le post
        $item->delete();

        // Notification
        $notification = [
            'message' => 'BlogPost Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    public function BlogDetails($slug)
    {
        $blog = BlogPost::where('post_slug', $slug)->first();
        $bCategory = BlogCategory::latest()->get();
        $lPost = BlogPost::latest()->limit(3)->get();

        return view('frontend.blog.blog_details', compact('blog', 'bCategory', 'lPost'));
    }
    public function BlogCatList($id)
    {
         
        // get the specifique blog category
        //  $blog = BlogPost::whereIn('blogcat_id', [1,2,3])->get();
        
        $blog = BlogPost::where('blogcat_id', $id)->get();
        $nameCat = BlogCategory::where('id', $id)->first();
       
        $bCategory = BlogCategory::latest()->get();
        $lPost = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_cat_list', compact('blog', 'bCategory', 'lPost', 'nameCat'));
    }


    
}
