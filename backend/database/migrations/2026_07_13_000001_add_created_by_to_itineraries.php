<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute created_by_user_id : nullable = itinéraire officiel Athar (créé par
 * un admin ou le seeder). Non-nul = proposé par un utilisateur inscrit,
 * publié immédiatement avec un badge « Proposé par la communauté ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')->nullable()->after('cover_image')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_user_id');
        });
    }
};
