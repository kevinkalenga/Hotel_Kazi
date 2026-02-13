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
}
