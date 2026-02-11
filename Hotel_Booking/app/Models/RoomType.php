<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
     //All our field will be fillable
     protected $guarded = [];

    public function rooms()
    {
     // Un RoomType a plusieurs chambres, donc ce n’est pas belongsTo, mais hasMany.
       return $this->hasMany(Room::class, 'roomtype_id', 'id');
    }
}
