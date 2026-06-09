<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // Tous les champs peuvent être assignés
    protected $guarded = [];

    /**
     * Relation : chaque Room appartient à un type de chambre
     * 
     * Room → RoomType (BelongsTo)
     * 
     * Cette relation récupère le type de la chambre via 'roomtype_id'.
     * Utile pour accéder aux caractéristiques générales du type (nom, description, etc.).
     */
    public function type()
    {
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'id');
    }

    /**
     * Relation : un Room peut avoir plusieurs images
     * 
     * Room → MultiImage (HasMany)
     * 
     * Permet de récupérer toutes les images associées à cette chambre.
     * La clé étrangère dans la table multi_images est 'rooms_id'.
     */
    public function multiImages()
    {
        return $this->hasMany(MultiImage::class, 'rooms_id', 'id');
    }

     /**
     * Relation : un Room peut avoir plusieurs équipements ou installations
     * 
     * Room → Facility (HasMany)
     * 
     * Permet de récupérer toutes les facilités/équipements associés à cette chambre.
     * La clé étrangère dans la table facilities est 'rooms_id'.
     */
    public function facilities()
    {
        return $this->hasMany(Facility::class, 'rooms_id', 'id');
    }

     /**
     * Relation : un Room peut avoir plusieurs RoomNumbers (numéros de chambre)
     * 
     * Room → RoomNumber (HasMany)
     * 
     * Permet de récupérer tous les numéros de chambres associés à ce Room.
     * Filtrage : on récupère seulement ceux dont le statut est 'active'.
     */
    public function roomNumbers()
    {
        return $this->hasMany(RoomNumber::class, 'rooms_id', 'id')->where('status', 'active');
    }
}

