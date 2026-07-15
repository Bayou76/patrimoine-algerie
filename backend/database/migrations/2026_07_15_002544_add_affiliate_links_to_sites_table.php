<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Liens d'affiliation optionnels par site : GetYourGuide (activités) et
 * Booking.com (hébergement). Nullable — la plupart des sites n'en auront
 * pas, seuls ceux avec une offre d'affiliation pertinente affichent le
 * bouton correspondant côté frontend.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('affiliate_activity_url')->nullable()->after('entry_fee');
            $table->string('affiliate_hotel_url')->nullable()->after('affiliate_activity_url');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['affiliate_activity_url', 'affiliate_hotel_url']);
        });
    }
};
