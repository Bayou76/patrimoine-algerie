<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute unesco_year : année d'inscription au patrimoine mondial UNESCO.
 * Nullable = le site n'est pas inscrit. Une valeur = "Inscrit UNESCO depuis {année}".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedSmallInteger('unesco_year')->nullable()->after('entry_fee');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('unesco_year');
        });
    }
};
