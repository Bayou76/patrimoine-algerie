<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle SiteImage — une image de la galerie d'un site (photos secondaires).
 * L'image principale est stockée directement sur Site::image_path.
 * `position` sert à trier les images côté galerie.
 */
class SiteImage extends Model
{
    protected $fillable = [
        'site_id',
        'path',
        'caption',
        'position',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
