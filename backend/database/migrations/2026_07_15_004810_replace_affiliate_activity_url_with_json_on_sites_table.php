<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Remplace le champ unique affiliate_activity_url par un tableau JSON
 * affiliate_activities ([{label, url}, ...]) : un site peut avoir plusieurs
 * offres d'activités affiliées (ex: une version "petit budget" et une
 * "premium avec déjeuner"), pas une seule.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->json('affiliate_activities')->nullable()->after('affiliate_activity_url');
        });

        // Migre les valeurs existantes (un seul lien par site jusqu'ici)
        // vers le nouveau format tableau, pour ne rien perdre.
        DB::table('sites')->whereNotNull('affiliate_activity_url')->get(['id', 'affiliate_activity_url'])
            ->each(function ($site) {
                DB::table('sites')->where('id', $site->id)->update([
                    'affiliate_activities' => json_encode([
                        ['label' => 'Réserver une activité', 'url' => $site->affiliate_activity_url],
                    ]),
                ]);
            });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('affiliate_activity_url');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('affiliate_activity_url')->nullable()->after('affiliate_hotel_url');
        });

        DB::table('sites')->whereNotNull('affiliate_activities')->get(['id', 'affiliate_activities'])
            ->each(function ($site) {
                $activities = json_decode($site->affiliate_activities, true);
                DB::table('sites')->where('id', $site->id)->update([
                    'affiliate_activity_url' => $activities[0]['url'] ?? null,
                ]);
            });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('affiliate_activities');
        });
    }
};
