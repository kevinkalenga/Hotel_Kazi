<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\Booking;
use Auth;
use Stripe\Charge;
use App\Models\BookingRoomList;
use App\Models\RoomNumber;
use App\Models\RoomType;

class RoomListController extends Controller
{
    public function ViewRoomList()
    {
        // On récupère tous les numéros de chambre avec le type de chambre
        // et la dernière réservation (si elle existe)
        $room_number_list = RoomNumber::with([
            'room_type',          // Relation vers RoomType
            'last_booking.booking' // Relation vers Booking via BookingRoomList
        ])->orderBy('room_type_id', 'asc')
          ->get();

        return view('backend.allroom.roomlist.view_roomlist', compact('room_number_list'));
    }


    public function AddRoomList(){

        $roomtype = RoomType::all();
        return view('backend.allroom.roomlist.add_roomlist',compact('roomtype'));

    }
}
