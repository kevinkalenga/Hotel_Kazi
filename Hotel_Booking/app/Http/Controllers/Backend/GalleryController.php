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


    public function EditGallery($id){

        $gallery = Gallery::find($id);
        return view('backend.gallery.edit_gallery',compact('gallery'));

    }


    public function UpdateGallery(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        if ($request->file('photo_name')) {

            // delete old image
            unlink(public_path($gallery->photo_name));

            $img = $request->file('photo_name');
            $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();

            $img->move(public_path('upload/gallery'), $name_gen);

            $gallery->photo_name = 'upload/gallery/'.$name_gen;
            $gallery->save();
        }

         $notification = [
            'message' => 'Gallery Updated Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->route('all.gallery')->with($notification);
    }


    public function DeleteGallery($id)
    {
       $gallery = Gallery::findOrFail($id);

       // Supprimer l'image du dossier
       if (file_exists(public_path($gallery->photo_name))) {
           unlink(public_path($gallery->photo_name));
       }

       // Supprimer en base
       $gallery->delete();

       $notification = [
           'message' => 'Gallery Deleted Successfully',
           'alert-type' => 'success'
       ];

       return redirect()->back()->with($notification);
    }

    public function DeleteGalleryMultiple(Request $request)
    {
        $selectedItems = $request->input('selectedItem', []);

        if (!empty($selectedItems)) {

            foreach ($selectedItems as $itemId) {

                $item = Gallery::find($itemId);

                if ($item) {

                    $imgPath = public_path($item->photo_name);

                    // Vérifier si le fichier existe
                    if (file_exists($imgPath)) {
                        unlink($imgPath);
                    }

                    $item->delete();
                }
            }
        }

        $notification = [
            'message' => 'Selected Images Deleted Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }


    // Show gallery in front
     public function ShowGallery(){
        $gallery = Gallery::latest()->get();
        return view('frontend.gallery.show_gallery',compact('gallery'));
     }
}
