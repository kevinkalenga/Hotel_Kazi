<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Gallery;

class GalleryController extends Controller
{
    public function AllGallery(){


      $gallery = Gallery::latest()->get();
        
       return view('backend.gallery.all_gallery', compact('gallery'));

    } 


    public function AddGallery(){
        return view('backend.gallery.add_gallery');
    }


    public function StoreGallery(Request $request)
    {
        // Validation
        $request->validate([
            'photo_name' => 'required',
            'photo_name.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

         $images = $request->file('photo_name');

        if ($images) {

            foreach ($images as $img) {

                $name_gen = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();

                // Correction Intervention Image v3
                $manager = new ImageManager(new Driver());
                $image = $manager->read($img);
                $image->resize(550, 550)->save(public_path('upload/gallery/' . $name_gen));

                $save_url = 'upload/gallery/' . $name_gen;

                Gallery::create([
                    'photo_name' => $save_url,
                    'created_at' => Carbon::now(),
                ]);
            }
        }

        $notification = [
            'message' => 'Gallery Inserted Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->route('all.gallery')->with($notification);
    }
}
