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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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


//   public function StoreRoomList(Request $request)
//   {
    
    
//     // Validation côté serveur pour les champs obligatoires
//     $request->validate([
//         'roomtype_id' => 'required|exists:room_types,id',
//         'check_in' => 'required|date',
//         'check_out' => 'required|date|after:check_in',
//         'number_of_rooms' => 'required|integer|min:1',
//         'number_of_person' => 'required|integer|min:1',
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|max:255',
//         'phone' => 'required|string|max:20',
//         'address' => 'required|string|max:500',
//     ]);

//     // Vérifier si check_in == check_out
//     if ($request->check_in == $request->check_out) {
//         return redirect()->back()->withInput()->with([
//             'message' => 'You Entered Same Date',
//             'alert-type' => 'error'
//         ]);
//     }

//     DB::beginTransaction();

//     try {
//         $room = Room::withCount('roomNumbers')->where('roomtype_id', $request->roomtype_id)->firstOrFail();

//         $total_rooms = $room->room_numbers_count; 

//         // Recalculer la disponibilité côté serveur
//         $sdate = Carbon::parse($request->check_in);
//         $edate = Carbon::parse($request->check_out)->subDay(); // ne pas compter le check-out
//         $period = CarbonPeriod::create($sdate, $edate);

//         $dates = collect($period)->map->format('Y-m-d');

//         $maxBooked = 0;

//         foreach ($dates as $date) {
//               $bookedForDate = RoomBookedDate::where('room_id', $room->id)
//              ->count(); // ou sum('number_of_rooms') si tu as cette colonne
//                $maxBooked = max($maxBooked, $bookedForDate);
//         }

 

//        $available_rooms = $total_rooms - $maxBooked;



//         if ($available_rooms < $request->number_of_rooms) {
//             return redirect()->back()->withInput()->with([
//                 'message' => 'You Entered More Rooms Than Available!',
//                 'alert-type' => 'error'
//             ]);
//         }

//         // Vérifier la capacité de la chambre
//         if ($room->room_capacity < $request->number_of_person) {
//             return redirect()->back()->withInput()->with([
//                 'message' => 'You Entered More Guests Than Room Capacity!',
//                 'alert-type' => 'error'
//             ]);
//         }

//         // Calcul des nuits et du prix
//         $total_nights = $sdate->diffInDays(Carbon::parse($request->check_out));
//         $subtotal = $room->price * $total_nights * $request->number_of_rooms;
//         $discount = ($room->discount / 100) * $subtotal;
//         $total_price = $subtotal - $discount;

//         // Code de réservation unique
//         $code = Str::upper(Str::random(9));

//         // Créer la réservation
//         $booking = Booking::create([
//             'rooms_id' => $room->id,
//             'user_id' => Auth::id(),
//             'check_in' => $sdate->format('Y-m-d'),
//             'check_out' => Carbon::parse($request->check_out)->format('Y-m-d'),
//             'number_of_rooms' => $request->number_of_rooms,
//             'person' => $request->number_of_person,
//             'total_night' => $total_nights,
//             'actual_price' => $room->price,
//             'subtotal' => $subtotal,
//             'discount' => $discount,
//             'total_price' => $total_price,
//             'payment_method' => 'COD',
//             'payment_status' => 0,
//             'name' => $request->name,
//             'email' => $request->email,
//             'phone' => $request->phone,
//             'country' => $request->country,
//             'state' => $request->state,
//             'zip_code' => $request->zip_code,
//             'address' => $request->address,
//             'code' => $code,
//             'status' => 0,
//             'created_at' => now(),
//         ]);

//         // Enregistrer les dates de réservation
//         foreach ($period as $date) {
//             RoomBookedDate::create([
//                 'booking_id' => $booking->id,
//                 'room_id' => $room->id,
//                 'book_date' => $date->format('Y-m-d'),
//             ]);
//         }

//         DB::commit();

//         return redirect()->back()->with([
//             'message' => 'Booking Added Successfully',
//             'alert-type' => 'success'
//         ]);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->withInput()->with([
//             'message' => 'Something went wrong: ' . $e->getMessage(),
//             'alert-type' => 'error'
//         ]);
//     }
//   }

    
    public function StoreRoomList(Request $request)
{
    // Validation des champs
    $request->validate([
        'roomtype_id' => 'required|exists:room_types,id',
        'check_in' => 'required|date',
        'check_out' => 'required|date|after:check_in',
        'number_of_rooms' => 'required|integer|min:1',
        'number_of_person' => 'required|integer|min:1',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
    ]);

    // Vérifier si check_in == check_out
    if ($request->check_in == $request->check_out) {
        return redirect()->back()->withInput()->with([
            'message' => 'You Entered Same Date',
            'alert-type' => 'error'
        ]);
    }

    DB::beginTransaction();

    try {
        // On récupère le premier room actif pour le type choisi
        $room = Room::withCount('roomNumbers')
            ->where('roomtype_id', $request->roomtype_id)
            ->firstOrFail();

        $total_rooms = $room->room_numbers_count; // total de chambres actives

        // Période de réservation (multi-nuits)
        $sdate = Carbon::parse($request->check_in);
        $edate = Carbon::parse($request->check_out)->subDay(); // ne pas compter le check-out
        $period = CarbonPeriod::create($sdate, $edate);
        $dates = collect($period)->map->format('Y-m-d');

        // Calcul de la disponibilité
        $maxBooked = 0;
        foreach ($dates as $date) {
            // Nombre de chambres réservées ce jour
            $bookedForDate = RoomBookedDate::where('room_id', $room->id)
                ->where('book_date', $date)
                ->count(); // ou sum('number_of_rooms') si tu as cette colonne
            $maxBooked = max($maxBooked, $bookedForDate);
        }

        $available_rooms = $total_rooms - $maxBooked;

        if ($available_rooms < $request->number_of_rooms) {
            return redirect()->back()->withInput()->with([
                'message' => 'You Entered More Rooms Than Available!',
                'alert-type' => 'error'
            ]);
        }

        // Vérifier la capacité de la chambre
        if ($room->room_capacity < $request->number_of_person) {
            return redirect()->back()->withInput()->with([
                'message' => 'You Entered More Guests Than Room Capacity!',
                'alert-type' => 'error'
            ]);
        }

        // Calcul des nuits et du prix
        $total_nights = $sdate->diffInDays(Carbon::parse($request->check_out));
        $subtotal = $room->price * $total_nights * $request->number_of_rooms;
        $discount = ($room->discount / 100) * $subtotal;
        $total_price = $subtotal - $discount;

        // Code de réservation unique
        $code = Str::upper(Str::random(9));

        // Créer la réservation
        $booking = Booking::create([
            'rooms_id' => $room->id,
            'user_id' => Auth::id(),
            'check_in' => $sdate->format('Y-m-d'),
            'check_out' => Carbon::parse($request->check_out)->format('Y-m-d'),
            'number_of_rooms' => $request->number_of_rooms,
            'persion' => $request->number_of_person,
            'total_night' => $total_nights,
            'actual_price' => $room->price,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total_price' => $total_price,
            'payment_method' => 'COD',
            'payment_status' => 0,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'code' => $code,
            'status' => 0,
            'created_at' => now(),
        ]);

        // Enregistrer les dates de réservation
        foreach ($dates as $date) {
            RoomBookedDate::create([
                'booking_id' => $booking->id,
                'room_id' => $room->id,
                'book_date' => $date,
            ]);
        }

        DB::commit();

        return redirect()->back()->with([
            'message' => 'Booking Added Successfully',
            'alert-type' => 'success'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with([
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'alert-type' => 'error'
        ]);
    }
}

    

}
