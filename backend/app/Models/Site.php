<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Site — un lieu du patrimoine algérien (Timgad, Casbah, Tassili…).
 *
 * En Laravel, un modèle Eloquent représente une table de la base de données.
 * Ici : la table `sites`. Chaque instance de Site = une ligne de cette table.
 *
 * Les données textuelles (nom, histoire, description) ne sont pas ici mais
 * dans SiteTranslation, pour supporter le multilingue (fr/ar/en).
 */
class Site extends Model
{
    // Champs qu'on autorise à assigner en masse via ::create() ou ->fill().
    // Sécurité : sans ça, un attaquant pourrait injecter n'importe quel champ.
    protected $fillable = [
        'latitude',
        'longitude',
        'category',
        'wilaya',
        'image_path',
        'slug',
        'opening_hours',
        'entry_fee',
        'unesco_year',
        'affiliate_activities',
        'affiliate_hotel_url',
    ];

    // Conversions automatiques de types à la lecture/écriture.
    // Sans ce cast, latitude/longitude seraient des strings côté PHP, et
    // affiliate_activities un texte JSON brut plutôt qu'un vrai tableau PHP.
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'affiliate_activities' => 'array',
    ];

    // --- Relations ---
    // hasMany = « un site a plusieurs X » (1-N).

    /** Traductions dans les 3 langues supportées. */
    public function translations()
    {
        return $this->hasMany(SiteTranslation::class);
    }

    /** Avis laissés par les utilisateurs sur ce site. */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** Photos de la galerie, ordonnées par la colonne `position`. */
    public function images()
    {
        return $this->hasMany(SiteImage::class)->orderBy('position');
    }

    /** Événements historiques rattachés (pour la chronologie), triés par année. */
    public function timelineEvents()
    {
        return $this->hasMany(SiteTimelineEvent::class)->orderBy('year');
    }

    /** Favoris + « déjà visités » posés par les utilisateurs connectés. */
    public function userInteractions()
    {
        return $this->hasMany(SiteUserInteraction::class);
    }

    /**
     * Retourne la traduction pour une langue donnée, avec fallback vers le français.
     * Utile pour ne jamais renvoyer null si le site n'est pas traduit en arabe/anglais.
     */
    public function translation(string $languageCode): ?SiteTranslation
    {
        return $this->translations
            ->firstWhere('language_code', $languageCode)
            ?? $this->translations->firstWhere('language_code', 'fr');
    }

    /** Note moyenne calculée en PHP à partir des avis chargés. */
    public function averageRating(): ?float
    {
        return $this->reviews->avg('rating');
    }
}
