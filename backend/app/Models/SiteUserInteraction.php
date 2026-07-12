<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle SiteUserInteraction — la relation entre un utilisateur et un site.
 *
 * Une seule ligne par couple (user_id, site_id) stocke à la fois :
 *   - is_favorite : ❤️ Ajouté aux favoris ?
 *   - is_visited  : ✅ Déjà visité en vrai ?
 *
 * On a choisi une seule table avec 2 booléens plutôt que 2 tables (favorites +
 * visited) pour économiser un JOIN quand on veut savoir « qu'est-ce que
 * l'utilisateur a fait avec ce site ».
 */
class SiteUserInteraction extends Model
{
    protected $fillable = [
        'site_id',
        'user_id',
        'is_favorite',
        'is_visited',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_visited' => 'boolean',
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
