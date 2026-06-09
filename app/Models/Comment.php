<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
     protected $guarded = [];

     /**
      * Récupère l'utilisateur associé à ce commentaire.
      *
      * Cette méthode définit une relation inverse "belongsTo" :
      * chaque commentaire appartient à un seul utilisateur.
      * La clé étrangère est 'user_id' dans la table comments,
      * qui fait référence à la colonne 'id' de la table users.
      */

     public function user()
     {
          return $this->belongsTo(User::class, 'user_id', 'id');
     }


     public function post(){
        return $this->belongsTo(BlogPost::class,'post_id','id');
     }
}
