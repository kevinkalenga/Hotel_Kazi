<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Facility;

class RoomController extends Controller
{
    public function EditRoom($id)
    {
       // Get the room by ID
    $editData = Room::findOrFail($id);

    // Decode the facility_name JSON column (assuming it stores multiple facilities)
    // If it’s stored as plain text for single facility, you can wrap it in an array
    $basic_facility = json_decode($editData->facility_name, true) ?? [$editData->facility_name];

    return view('backend.allroom.rooms.edit_rooms', compact('editData', 'basic_facility'));
    }
}

