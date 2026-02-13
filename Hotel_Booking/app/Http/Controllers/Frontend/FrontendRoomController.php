<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use Carbon\Carbon;
use Intervention\Image\ImageManager;

class FrontendRoomController extends Controller
{
    public function AllFrontendRoomList()
    {
      $rooms = Room::latest()->get();
      return view('frontend.room.all_rooms', compact('rooms'));
    }
    public function RoomDetailsPage($id)
    {
      // Charger la room avec son type (utilisé dans le blade)
      $roomDetails = Room::with('type')->findOrFail($id);

      // Recharger systématiquement les images et facilities
      $multiImages = MultiImage::where('rooms_id', $id)->get();
      $facility    = Facility::where('rooms_id', $id)->get();

      return view('frontend.room.room_details', compact(
        'roomDetails',
        'multiImages',
        'facility'
      ));
    }

}
