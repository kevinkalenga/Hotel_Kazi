<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // Tous les champs peuvent être assignés
    protected $guarded = [];

    // Relation vers RoomType
    public function type()
    {
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'id');
    }

    // Relation vers MultiImage
    public function multiImages()
    {
        return $this->hasMany(MultiImage::class, 'rooms_id', 'id');
    }

    // Relation vers Facility
    public function facilities()
    {
        return $this->hasMany(Facility::class, 'rooms_id', 'id');
    }

    // Relation vers RoomNumber
    public function roomNumbers()
    {
        return $this->hasMany(RoomNumber::class, 'rooms_id', 'id');
    }
}

