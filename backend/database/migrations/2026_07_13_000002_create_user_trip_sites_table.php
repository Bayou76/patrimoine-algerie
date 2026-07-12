<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * « Mon voyage » — itinéraire personnel privé d'un utilisateur, construit à
 * partir de ses favoris. Un seul voyage par utilisateur (pas de multi-trips
 * pour rester simple) : une liste ordonnée de sites avec note personnelle.
 *
 * Différent des itinéraires (table itineraries) qui sont publics/partagés :
 * ceci n'est visible que par son propriétaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_trip_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('note', 500)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_trip_sites');
    }
};
