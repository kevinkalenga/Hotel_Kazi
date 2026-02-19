<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
     //All our field will be fillable
     protected $guarded = [];

     public function assign_rooms()
     {
          return $this->hasMany(BookingRoomList::class, 'booking_id');
     }
     public function user()
     {
          // A booking belongs to a user
          return $this->belongsTo(User::class);
     }
     public function room()
     {
          // A booking belongs to a room
          return $this->belongsTo(Room::class, 'rooms_id', 'id');
     }
}
