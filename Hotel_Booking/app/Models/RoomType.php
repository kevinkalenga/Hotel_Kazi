<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
     //All our field will be fillable
     protected $guarded = [];

     public function room()
     {
          return $this->belongsTo(Room::class, 'id', 'roomtype_id');
     }
}
