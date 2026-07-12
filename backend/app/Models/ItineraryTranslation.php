<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle ItineraryTranslation — même principe que SiteTranslation mais pour
 * les itinéraires : titre, résumé, description dans une langue donnée.
 */
class ItineraryTranslation extends Model
{
    protected $fillable = [
        'itinerary_id',
        'language_code',
        'title',
        'summary',
        'description',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }
}
