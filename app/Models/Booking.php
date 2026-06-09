<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
     //All our field will be fillable
     protected $guarded = [];

     public function assign_rooms()
     {
          /**
          * Relation : une réservation peut avoir plusieurs chambres assignées
          * 
          * Booking → BookingRoomList (Many)
          * Chaque réservation peut être associée à plusieurs lignes dans la table pivot
          * booking_room_lists, qui relie une réservation à une ou plusieurs RoomNumbers.
          */
          return $this->hasMany(BookingRoomList::class, 'booking_id');
     }
     public function user()
     {
         /**
          * Relation : une réservation appartient à un utilisateur
          * 
          * Booking → User (BelongsTo)
          * Permet de récupérer les informations de l'utilisateur qui a effectué la réservation.
        */
          return $this->belongsTo(User::class);
     }
     public function room()
     {
          /**
            * Relation : une réservation appartient à une chambre principale (Room)
            * 
            * Booking → Room (BelongsTo)
            * Cette relation récupère le Room principal lié à la réservation via la clé rooms_id.
          */
          return $this->belongsTo(Room::class, 'rooms_id', 'id');
     }
}
