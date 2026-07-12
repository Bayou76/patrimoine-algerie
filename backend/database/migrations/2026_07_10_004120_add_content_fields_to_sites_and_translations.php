<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('opening_hours')->nullable();
            $table->string('entry_fee')->nullable();
        });

        Schema::table('site_translations', function (Blueprint $table) {
            $table->text('history')->nullable();
            $table->text('visit_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['opening_hours', 'entry_fee']);
        });

        Schema::table('site_translations', function (Blueprint $table) {
            $table->dropColumn(['history', 'visit_info']);
        });
    }
};
