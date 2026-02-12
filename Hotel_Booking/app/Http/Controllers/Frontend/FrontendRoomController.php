<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use App\Models\Room;
use Carbon\Carbon;
use Intervention\Image\ImageManager;

class FrontendRoomController extends Controller
{
    public function AllFrontendRoomList()
    {
      $rooms = Room::latest()->get();
      return view('frontend.room.all_rooms', compact('rooms'));
    }
}
