<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\BookArea;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Room;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\RoomBookedDate;
use App\Models\Booking;
use Auth;
use Stripe\Stripe;
use Stripe\Charge;

class BookingController extends Controller
{
    public function Checkout()
    {
        
       
        // verifie la session existante ou pas
        if (Session::has('book_date')) {
            // recup de session data
           $book_data = Session::get('book_date');
           $room = Room::find($book_data['room_id']);
           

           $toDate = Carbon::parse($book_data['check_in']);
           $fromDate = Carbon::parse($book_data['check_out']);
           $nights = $toDate->diffInDays($fromDate);

           return view('frontend.checkout.checkout',compact('book_data','room','nights'));
        }else{

            $notification = array(
                'message' => 'Something want to wrong!',
                'alert-type' => 'error'
            ); 
            return redirect('/')->with($notification); 
        } // end else
    
    
    }
    public function BookingStore(Request $request)
    {
        
        $validateData = $request->validate([
            'check_in' => 'required',
            'check_out' => 'required',
            'persion' => 'required',
            'number_of_rooms' => 'required',

        ]);

        if ($request->available_room < $request->number_of_rooms) {
           
            $notification = array(
                'message' => 'Something went wrong!',
                'alert-type' => 'error'
            ); 
            return redirect()->back()->with($notification); 
        }
        // remove the session
        Session::forget('book_date');

        $data = array();
        $data['number_of_rooms'] = $request->number_of_rooms;
        $data['available_room'] = $request->available_room;
        $data['persion'] = $request->persion;
        $data['check_in'] = date('Y-m-d',strtotime($request->check_in));
        $data['check_out'] = date('Y-m-d',strtotime($request->check_out));
        $data['room_id'] = $request->room_id;
        
        // put everything into the session
        Session::put('book_date',$data);

        return redirect()->route('checkout');
    
    }


    public function CheckoutStore(Request $request){

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required', 
        ]);
          //  Get all the data from the session  
           $book_data = Session::get('book_date'); 
           $toDate = Carbon::parse($book_data['check_in']);
           $fromDate = Carbon::parse($book_data['check_out']);
           $total_nights = $toDate->diffInDays($fromDate);
           
           // Find a room from the book_data and romm_id because we save it in the session   
           $room = Room::find($book_data['room_id']);
          // number_of_rooms is also save in the session  
           $subtotal = $room->price * $total_nights * $book_data['number_of_rooms'] ;
           $discount = ($room->discount/100)*$subtotal;
           $total_price = $subtotal-$discount;
           $code = rand(000000000,999999999);

          // On vérifie si le moyen de paiement choisi est Stripe 

            if ($request->payment_method === 'Stripe') {

                try {
                    //  Définition de la clé SECRÈTE Stripe (obligatoire côté backend)

                     Stripe::setApiKey(config('services.stripe.secret'));

                        // Création du paiement Stripe
                        // Stripe attend le montant en CENTIMES (ex: 10$ = 1000)

                    $s_pay = Charge::create([
                        'amount' => intval($total_price * 100), // toujours en centimes
                        'currency' => 'usd',
                        'source' => $request->stripeToken, // token généré côté frontend
                        'description' => 'Payment For Booking. Booking No ' . $code,
                    ]);
                     
                      // Vérification si le paiement a réussi
                    if ($s_pay->status === 'succeeded') {
                         // 1 = paiement effectué avec succès
                        $payment_status = 1;
                          // ID unique de la transaction Stripe (à stocker en base)
                        $transaction_id = $s_pay->id;
                    }

                } catch (\Exception $e) {
                    // En cas d'erreur Stripe (carte refusée, token invalide, etc.)
                    $notification = [
                        'message' => $e->getMessage(),
                        'alert-type' => 'error'
                    ];

                    return redirect('/')->with($notification);
                }

            } else {
                 //Cas paiement Cash On Delivery (pas de Stripe)
                $payment_status = 0;
                $transaction_id = null;
            }

            //   Insert the data into the booking table 
           $data = new Booking();
           $data->rooms_id = $room->id;
           $data->user_id = Auth::user()->id;
           $data->check_in = date('Y-m-d',strtotime($book_data['check_in']));
           $data->check_out = date('Y-m-d',strtotime($book_data['check_out']));
           $data->persion = $book_data['persion'];
           $data->number_of_rooms = $book_data['number_of_rooms'];
           $data->total_night = $total_nights;

           $data->actual_price = $room->price;
           $data->subtotal = $subtotal;
           $data->discount = $discount;
           $data->total_price = $total_price;
           $data->payment_method = $request->payment_method;
           $data->transaction_id = '';
           $data->payment_status = 0;

           $data->name = $request->name;
           $data->email = $request->email;
           $data->phone = $request->phone;
           $data->country = $request->country;
           $data->state = $request->state;
           $data->zip_code = $request->zip_code;
           $data->address = $request->address;

           $data->code = $code;
           $data->status = 0;
           $data->created_at = Carbon::now();
           $data->save();

           

           // Insert data into the room_booked_date table  
           $sdate = date('Y-m-d',strtotime($book_data['check_in']));
           $edate = date('Y-m-d',strtotime($book_data['check_out']));
           $eldate = Carbon::create($edate)->subDay();
           $d_period = CarbonPeriod::create($sdate,$eldate);
            foreach ($d_period as $period) {
               $booked_dates = new RoomBookedDate();
               $booked_dates->booking_id = $data->id;
               $booked_dates->room_id = $room->id;
               $booked_dates->book_date = date('Y-m-d', strtotime($period));
               $booked_dates->save();
            }
            
            // remove the data from the session after saving in db
           Session::forget('book_date');


            $notification = array(
               'message' => 'Booking Added Successfully',
               'alert-type' => 'success'
            ); 
           
            return redirect('/')->with($notification); 




    }

    public function BookingList()
    {
        $allData = Booking::orderBy('id', 'desc')->get();
        return view('backend.booking.booking_list', compact('allData'));
    }
}
