<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle UserTripSite — une étape du voyage personnel d'un utilisateur.
 * Une ligne = un site dans le « Mon voyage » de cet utilisateur, avec sa
 * position dans le parcours et une note personnelle optionnelle.
 */
class UserTripSite extends Model
{
    protected $fillable = [
        'user_id',
        'site_id',
        'position',
        'note',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
