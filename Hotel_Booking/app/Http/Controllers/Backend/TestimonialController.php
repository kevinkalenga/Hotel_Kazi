<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class TestimonialController extends Controller
{
    public function AllTestimonial()
    {
        $testimonial = Testimonial::latest()->get();
        return view('backend.testimonial.all_testimonial', compact('testimonial'));
    }

    public function AddTestimonial(){
        return view('backend.testimonial.add_testimonial');
    }

   

  public function StoreTestimonial(Request $request)
  {
    // Validation
    $request->validate([
        'name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // Image
    $image = $request->file('image');
    $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

    $manager = new ImageManager(new Driver());

    $img = $manager->read($image);
    $img->resize(50, 50);
    $img->save(public_path('upload/testimonial/' . $name_gen));

    $save_url = 'upload/testimonial/' . $name_gen;

    // Save to DB
    Testimonial::create([
        'name' => $request->name,
        'city' => $request->city,
        'message' => $request->message,
        'image' => $save_url,
    ]);

    return redirect()
        ->route('all.testimonial')
        ->with([
            'message' => 'Testimonial Data Inserted Successfully',
            'alert-type' => 'success'
        ]);
  }

    public function EditTestimonial($id){

        $testimonial = Testimonial::find($id);
        return view('backend.testimonial.edit_testimonial',compact('testimonial'));

    }


  public function UpdateTestimonial(Request $request)
  {
    // Validation
    $request->validate([
        'id' => 'required|exists:testimonials,id',
        'name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $testimonial = Testimonial::findOrFail($request->id);

    // Si une nouvelle image est uploadée
    if ($request->hasFile('image')) {

        // Supprimer l’ancienne image
        if ($testimonial->image && file_exists(public_path($testimonial->image))) {
            unlink(public_path($testimonial->image));
        }

        // Nouvelle image
        $image = $request->file('image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);
        $img->resize(50, 50);
        $img->save(public_path('upload/testimonial/' . $name_gen));

        $testimonial->image = 'upload/testimonial/' . $name_gen;
    }

    // Update des champs
    $testimonial->update([
        'name' => $request->name,
        'city' => $request->city,
        'message' => $request->message,
    ]);

    return redirect()
        ->route('all.testimonial')
        ->with([
            'message' => 'Testimonial Updated Successfully',
            'alert-type' => 'success'
        ]);
  }
}
