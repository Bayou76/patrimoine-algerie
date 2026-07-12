<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle SiteTranslation — le contenu textuel d'un site dans une langue.
 *
 * Pattern « table pivot de traduction » : au lieu de mettre `name_fr`, `name_ar`,
 * `name_en` dans la table sites, on crée une table sites_translations avec
 * une ligne par (site_id, language_code). Ça permet d'ajouter facilement
 * une 4e langue sans migration lourde.
 */
class SiteTranslation extends Model
{
    protected $fillable = [
        'site_id',
        'language_code',
        'name',
        'description',
        'history',
        'visit_info',
    ];

    /** belongsTo = relation inverse de hasMany : cette traduction appartient à un site. */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
