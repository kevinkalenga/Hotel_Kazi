<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookArea;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\MultiImage;
use App\Models\Facility;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

      // Other rooms 
      $otherRooms = Room::where('id', '!=', $id)->orderBy('id', 'DESC')->limit(2)->get();

      return view('frontend.room.room_details', compact(
        'roomDetails',
        'multiImages',
        'facility',
        'otherRooms'
      ));
    }

    public function BookingSearch(Request $request)
    {
      // flash is used to store data into the session for a single request
        $request->flash();
        if($request->check_in == $request->check_out) {
          $notification = array(
            'message' => 'Something went wrong',
            'alert-type' => 'error'
          );
           return redirect()->back()->with($notification);

        }

        $startDate = date('Y-m-d', strtotime($request->check_in));
        $endDate = date('Y-m-d', strtotime($request->check_out));
        $allDate = Carbon::create($endDate)->subDay();
        // count the days ou pediod of the booking
        $d_period = CarbonPeriod::create($startDate, $allDate);
        $dt_array = [];

        foreach($d_period as $period) {
          array_push($dt_array, date('Y-m-d', strtotime($period)));
        }

        $check_date_booking_ids = RoomBookedDate::whereIn('book_date', $dt_array)->distinct()->pluck('booking_id')->toArray();

        $rooms = Room::withCount('roomNumbers')->where('status', 1)->get();

        return view('frontend.room.search_room', compact('rooms', 'check_date_booking_ids'));
    }

}
