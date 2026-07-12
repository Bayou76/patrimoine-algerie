<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('duration'); // ex: "3 jours", "1 semaine"
            $table->string('difficulty')->nullable(); // facile / moyen / soutenu
            $table->string('theme'); // romain / naturel / spirituel / sud / villes
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        Schema::create('itinerary_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->string('language_code', 5);
            $table->string('title');
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->unique(['itinerary_id', 'language_code']);
        });

        Schema::create('itinerary_site', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('day_label')->nullable(); // ex: "Jour 1 — matin"
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['itinerary_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_site');
        Schema::dropIfExists('itinerary_translations');
        Schema::dropIfExists('itineraries');
    }
};
