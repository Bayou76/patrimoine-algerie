<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle SiteTimelineEvent — un événement historique rattaché à un site.
 *
 * Utilisé par la chronologie individuelle d'un site (ex: sur la page Timgad)
 * ET par la chronologie globale (/chronologie) qui agrège les 158 événements
 * de tous les sites triés par année.
 *
 * `year` est un entier signé : négatif = av. J.-C. Ça permet de trier
 * naturellement les événements de -20 000 000 (Hoggar) jusqu'à 2020.
 *
 * `period_label` est le libellé affiché (ex: « IIe siècle av. J.-C. ») car
 * `year` seul est peu parlant. Multilingue via language_code.
 */
class SiteTimelineEvent extends Model
{
    protected $fillable = [
        'site_id',
        'language_code',
        'year',
        'period_label',
        'title',
        'description',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
