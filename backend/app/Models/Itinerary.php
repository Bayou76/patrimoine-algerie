<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Itinerary — un itinéraire thématique (« Sur les traces des Romains »,
 * « Grand Sud saharien »…).
 *
 * Un itinéraire regroupe plusieurs sites dans un ordre défini, avec un thème
 * (romain / sud / villes / spirituel / naturel), une durée et une difficulté.
 * Les textes (titre, résumé, description) sont dans ItineraryTranslation
 * pour rester multilingues.
 */
class Itinerary extends Model
{
    protected $fillable = [
        'slug',
        'duration',
        'difficulty',
        'theme',
        'cover_image',
        'created_by_user_id',
    ];

    public function translations()
    {
        return $this->hasMany(ItineraryTranslation::class);
    }

    /** Auteur si proposé par un utilisateur ; null pour un itinéraire officiel. */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Sites qui composent l'itinéraire, ordonnés par leur position dans le parcours.
     *
     * belongsToMany = relation N-N via la table pivot `itinerary_site`.
     * withPivot() : on récupère aussi les colonnes supplémentaires de la pivot
     * (position, day_label, note) accessibles via $site->pivot->position.
     */
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'itinerary_site')
            ->withPivot(['position', 'day_label', 'note'])
            ->orderBy('itinerary_site.position');
    }
}
