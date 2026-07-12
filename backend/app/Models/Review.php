<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Review — un avis laissé par un utilisateur sur un site.
 *
 * Structure : rating (1-5), commentaire optionnel, is_verified qui indique
 * si l'utilisateur avait marqué le site comme « visité » avant d'écrire
 * (ça affiche un badge « Visite vérifiée » plus crédible).
 */
class Review extends Model
{
    protected $fillable = [
        'site_id',
        'user_id',
        'rating',
        'comment',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
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
