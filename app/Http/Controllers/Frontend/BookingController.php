<?php

namespace App\Http\Controllers\Frontend;

use Barryvdh\DomPDF\Facade\Pdf;
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
use App\Models\BookingRoomList;
use App\Models\RoomNumber;
use App\Models\User;
use App\Notifications\BookingComplete;
use Illuminate\Support\Facades\Notification;
use App\Mail\BookConfirm;
use Illuminate\Support\Facades\Mail;

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


   

     
   public function CheckoutStore(Request $request)
   {
        $user = User::where('role', 'admin')->get();
   
        // Validation des champs obligatoires côté serveur
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

        // Récupère les données de réservation stockées en session
        $book_data = Session::get('book_date'); 

        // Convertit les dates de check-in et check-out en objets Carbon pour manipulation facile
        $toDate = Carbon::parse($book_data['check_in']);
        $fromDate = Carbon::parse($book_data['check_out']);

        // Calcule le nombre total de nuits entre check-in et check-out
        $total_nights = $toDate->diffInDays($fromDate);

        // Récupère l'objet Room correspondant à l'id de la réservation
        $room = Room::find($book_data['room_id']);

        // Calcul du subtotal: prix d'une chambre * nombre de nuits * nombre de chambres
        $subtotal = $room->price * $total_nights * $book_data['number_of_rooms'];

        // Calcul du montant de la remise en fonction du pourcentage défini sur la chambre
        $discount = ($room->discount / 100) * $subtotal;

        // Calcul du total après application de la remise
        $total_price = $subtotal - $discount;

        // Génère un code unique pour la réservation
        $code = rand(000000000, 999999999);

        // Initialisation des variables pour le paiement
        $payment_status = 0;        // 0 = non payé, 1 = payé
        $transaction_id = null;     // ID de transaction (Stripe)

        // Si le client a choisi Stripe comme moyen de paiement
        if ($request->payment_method === 'Stripe') {
            try {
                // Configure la clé secrète Stripe côté backend
                Stripe::setApiKey(config('services.stripe.secret'));

                // Crée le paiement Stripe (montant en centimes)
                $s_pay = Charge::create([
                    'amount' => intval($total_price * 100),
                    'currency' => 'usd',
                    'source' => $request->stripeToken,
                    'description' => 'Payment For Booking. Booking No ' . $code,
                ]);

                // Vérifie si le paiement a réussi
                if ($s_pay->status === 'succeeded') {
                    $payment_status = 1;       // paiement réussi
                    $transaction_id = $s_pay->id; // récupère l'ID unique de Stripe
                } else {
                return redirect()->back()->with([
                    'message' => 'Payment failed',
                    'alert-type' => 'error'
                ]);
            }
            } catch (\Exception $e) {
                // En cas d'erreur Stripe (carte refusée, token invalide, etc.)
                $notification = [
                    'message' => $e->getMessage(),
                    'alert-type' => 'error'
                ];
                return redirect('/')->with($notification);
            }
        }

        // Création et insertion d'une nouvelle réservation dans la table bookings
        $data = new Booking();
        $data->rooms_id = $room->id;                      // ID de la chambre
        $data->user_id = Auth::user()->id;               // ID de l'utilisateur connecté
        $data->check_in = $toDate->format('Y-m-d');      // Check-in formaté
        $data->check_out = $fromDate->format('Y-m-d');   // Check-out formaté
        $data->persion = $book_data['persion'];          // Nombre de personnes
        $data->number_of_rooms = $book_data['number_of_rooms']; // Nombre de chambres réservées
        $data->total_night = $total_nights;             // Nombre de nuits
        $data->actual_price = $room->price;             // Prix par chambre
        $data->subtotal = $subtotal;                    // Sous-total (avant remise)
        $data->discount = $discount;                    // Remise
        $data->total_price = $total_price;             // Total final
        $data->payment_method = $request->payment_method; // Mode de paiement choisi
        $data->transaction_id = $transaction_id;        // ID de transaction (Stripe)
        $data->payment_status = $payment_status;        // Statut du paiement
        $data->name = $request->name;                   // Nom du client
        $data->email = $request->email;                 // Email du client
        $data->phone = $request->phone;                 // Téléphone
        $data->country = $request->country;             // Pays
        $data->state = $request->state;                 // Etat / région
        $data->zip_code = $request->zip_code;           // Code postal
        $data->address = $request->address;             // Adresse complète
        $data->code = $code;                             // Code unique de réservation
        $data->status = 0;                               // 0 = non confirmé / 1 = confirmé
        $data->created_at = Carbon::now();              // Date de création
        \Log::info('Stripe transaction ID: '.$transaction_id);
        $data->save();                                  // Enregistre la réservation en base

        // Enregistrement des dates réservées dans room_booked_dates
        $sdate = $toDate;                               // Date de départ
        $edate = $fromDate->subDay();                   // Dernière nuit (ne pas compter le check-out)
        $d_period = CarbonPeriod::create($sdate, $edate); // Période de réservation

        foreach ($d_period as $period) {
            $booked_dates = new RoomBookedDate();
            $booked_dates->booking_id = $data->id;      // Lien avec la réservation
            $booked_dates->room_id = $room->id;        // ID de la chambre
            $booked_dates->book_date = $period->format('Y-m-d'); // Date réservée
            $booked_dates->save();                      // Enregistre en base
        }

        // Supprime les données de session après la réservation
        Session::forget('book_date');

        // Notification succès
        $notification = [
        'message' => 'Booking Added Successfully',
        'alert-type' => 'success'
        ]; 

        //Notification 
        Notification::send($user, new BookingComplete($request->name));
    
        // Redirection vers la page d'accueil avec notification
        return redirect('/')->with($notification);
    }

    public function BookingList()
    {
        $allData = Booking::orderBy('id', 'desc')->get();
        return view('backend.booking.booking_list', compact('allData'));
    }

    public function EditBooking($id)
    {
          $editData = Booking::with('room')->find($id);
          return view('backend.booking.edit_booking',compact('editData'));
    }
    public function UpdateBookingStatus(Request $request, $id)
    {
         $booking = Booking::find($id);
         $booking->payment_status = $request->payment_status;
         $booking->status = $request->status;

         $booking->save();

        // Start Send Mail 
        $sendMail = Booking::find($id);

        $data = [
            'check_in' => $sendMail->check_in,
            'check_out' => $sendMail->check_out,
            'name' => $sendMail->name,
            'email' => $sendMail->email,
            'phone' => $sendMail->phone,
        ];

        Mail::to($sendMail->email)->send(new BookConfirm($data));

        
        
        $notification = array(
            'message' => 'Information Updated Successfully',
            'alert-type' => 'success'
        ); 
           
        return redirect()->back()->with($notification); 
    }
   

  public function UpdateBooking(Request $request, $id)
  {
    // Vérification disponibilité
    if ($request->available_room < $request->number_of_rooms) {
        return redirect()->back()->with([
            'message' => 'Not enough rooms available',
            'alert-type' => 'error'
        ]);
    }

    $booking = Booking::findOrFail($id);

    // Dates
    $checkIn  = Carbon::parse($request->check_in);
    $checkOut = Carbon::parse($request->check_out);

    //  Calcul correct du nombre de nuits
    $total_night = $checkIn->diffInDays($checkOut);

    // Sécurité
    if ($total_night < 1) {
        return redirect()->back()->with([
            'message' => 'Check-out must be after check-in',
            'alert-type' => 'error'
        ]);
    }

    // Prix
    $price = $booking->actual_price;
    $subtotal = $price * $request->number_of_rooms * $total_night;
    $discount = $booking->discount;
    $total_price = $subtotal - $discount;

    //  Mise à jour booking
    $booking->update([
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'number_of_rooms' => $request->number_of_rooms,
        'total_night' => $total_night,
        'subtotal' => $subtotal,
        'total_price' => $total_price,
    ]);

    // Nettoyage anciennes données
    BookingRoomList::where('booking_id', $id)->delete();
    RoomBookedDate::where('booking_id', $id)->delete();

    //  Réinsertion correcte des dates réservées
    $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

    foreach ($period as $date) {
        RoomBookedDate::create([
            'booking_id' => $booking->id,
            'room_id' => $booking->rooms_id,
            'book_date' => $date->format('Y-m-d'),
        ]);
    }

    return redirect()->back()->with([
        'message' => 'Booking Updated Successfully',
        'alert-type' => 'success'
    ]);
  }

    
    
    
    public function AssignRoom($booking_id)
    {
         // Récupère la réservation correspondant à l’ID fourni
        $booking = Booking::find($booking_id);
        
         // Récupère toutes les dates réservées pour cette réservation
        // et les stocke dans un tableau
        $booking_date_array = RoomBookedDate::where('booking_id',$booking_id)->pluck('book_date')->toArray();
        
        // Recherche toutes les réservations qui :
        // - ont une date identique à celles de la réservation actuelle
        // - concernent le même type de chambre
        // Puis récupère les IDs de réservation distincts
        $check_date_booking_ids = RoomBookedDate::whereIn('book_date',$booking_date_array)->where('room_id',$booking->rooms_id)->distinct()->pluck('booking_id')->toArray();
        
        // Récupère les IDs des réservations trouvées
        $booking_ids = Booking::whereIn('id',$check_date_booking_ids)->pluck('id')->toArray();
        
         // Récupère les IDs des numéros de chambre déjà assignés
         // à ces réservations
        $assign_room_ids = BookingRoomList::whereIn('booking_id',$booking_ids)->pluck('room_number_id')->toArray();
        

        // Récupère les chambres :
        // - du même type que la réservation
        // - non encore assignées
        // - avec un statut "Active"
        $room_numbers = RoomNumber::where('rooms_id',$booking->rooms_id)->whereNotIn('id',$assign_room_ids)->where('status','Active')->get();
        // Retourne la vue avec les données de la réservation
        // et la liste des chambres disponibles
        return view('backend.booking.assign_room',compact('booking','room_numbers'));
        

    }


    public function AssignRoomStore($booking_id,$room_number_id){

        $booking = Booking::find($booking_id);
        $check_data = BookingRoomList::where('booking_id',$booking_id)->count();

        if ($check_data < $booking->number_of_rooms) {
           $assign_data = new BookingRoomList();
           $assign_data->booking_id = $booking_id;
           $assign_data->room_id = $booking->rooms_id;
           $assign_data->room_number_id = $room_number_id;
           $assign_data->save();

           $notification = array(
            'message' => 'Room Assign Successfully',
            'alert-type' => 'success'
          ); 
            return redirect()->back()->with($notification);  
        } else {

            $notification = array(
                'message' => 'Room Already Assign',
                'alert-type' => 'error'
            ); 
            return redirect()->back()->with($notification);   

        }

    }

    public function AssignRoomDelete($id){

        $assign_room = BookingRoomList::find($id);
        $assign_room->delete();

        $notification = array(
            'message' => 'Assign Room Deleted Successfully',
            'alert-type' => 'success'
        ); 
        return redirect()->back()->with($notification); 

    }

    
    public function DownloadInvoice($id){

        $editData = Booking::with('room')->find($id);
        $pdf = Pdf::loadView('backend.booking.booking_invoice',compact('editData'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->stream('invoice.pdf');

     }

     public function UserBooking()
     {
        $id = Auth::user()->id;
        $allData = Booking::where('user_id', $id)->orderBy('id', 'desc')->get();

        return view('frontend.dashboard.user_booking', compact('allData'));
     }


     public function UserInvoice($id){

        $editData = Booking::with('room')->find($id);
        $pdf = Pdf::loadView('backend.booking.booking_invoice',compact('editData'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->stream('invoice.pdf');

     }


    public function MarkAsRead(Request $request , $notificationId){

        $user = Auth::user();
        $notification = $user->notifications()->where('id',$notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }

       return response()->json(['count' => $user->unreadNotifications()->count()]);

     }
    
}
