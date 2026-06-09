<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoomList extends Model
{
    //All our field will be fillable
     protected $guarded = [];

    
    
    /**
     * Relation : une ligne de réservation appartient à un numéro de chambre
     * 
     * BookingRoomList → RoomNumber (BelongsTo)
     * Chaque enregistrement de booking_room_lists est lié à une chambre spécifique
     * via la clé étrangère 'room_number_id'.
     */
    
     public function room_number(){
        return $this->belongsTo(RoomNumber::class,'room_number_id');
    }


    /**
     * Relation : une ligne de réservation appartient à une réservation
     * 
     * BookingRoomList → Booking (BelongsTo)
     * Chaque enregistrement appartient à une seule réservation via 'booking_id'.
     * Permet de remonter à la réservation principale pour récupérer les infos client, dates, etc.
     */

    public function booking(){
        return $this->belongsTo(Booking::class,'booking_id');
    }
}
