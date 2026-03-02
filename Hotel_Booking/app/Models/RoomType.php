<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
     //All our field will be fillable
     protected $guarded = [];

    
    
    /**
     * Relation : un type de chambre a plusieurs chambres
     * 
     * RoomType → Room (HasMany)
     * 
     * Chaque RoomType peut être lié à plusieurs chambres via la clé étrangère
     * 'roomtype_id' dans la table rooms. Cela permet de récupérer toutes les
     * chambres d’un type spécifique.
     */
     public function rooms()
    {
     // Un RoomType a plusieurs chambres, donc ce n’est pas belongsTo, mais hasMany.
       return $this->hasMany(Room::class, 'roomtype_id', 'id');
    }
}
