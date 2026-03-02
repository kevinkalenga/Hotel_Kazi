<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomNumber extends Model
{
    //All our field will be fillable
     protected $guarded = [];
    
    
      /**
     * Relation : le numéro de chambre appartient à un type de chambre
     * 
     * RoomNumber → RoomType (BelongsTo)
     * 
     * Cette relation permet de récupérer le type de la chambre
     * via la colonne 'room_type_id'. Utile pour afficher le nom ou les caractéristiques du type.
     */
    
    public function room_type()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    /**
     * Relation : récupérer la dernière réservation pour ce numéro de chambre
     * 
     * RoomNumber → BookingRoomList (HasOne)
     * 
     * Utilise 'latest()' pour obtenir la réservation la plus récente (par ID ou date créée)
     * sur cette chambre. Cela permet d’afficher la réservation active ou la dernière réservation passée.
     */
    public function last_booking()
    {
        return $this->hasOne(BookingRoomList::class, 'room_number_id')->latest();
    }
    
    /**
     * Relation : le numéro de chambre appartient à un Room
     * 
     * RoomNumber → Room (BelongsTo)
     * 
     * Chaque RoomNumber est associé à un Room principal via 'rooms_id'.
     * Permet de remonter pour récupérer le type global de la chambre, les images,
     * ou les équipements via la relation Room → RoomType.
     */
    public function room()
    {
      return $this->belongsTo(Room::class, 'rooms_id', 'id');
    }
}
