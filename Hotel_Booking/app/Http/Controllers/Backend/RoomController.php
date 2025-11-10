<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Carbon\Carbon;
use Intervention\Image\ImageManager;

class RoomController extends Controller
{
    public function EditRoom($id)
    {
       $editData = Room::find($id);
       return view('backend.allroom.rooms.edit_rooms', compact('editData'));
    }
}
