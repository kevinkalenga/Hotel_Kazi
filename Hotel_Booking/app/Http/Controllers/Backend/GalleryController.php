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
}
