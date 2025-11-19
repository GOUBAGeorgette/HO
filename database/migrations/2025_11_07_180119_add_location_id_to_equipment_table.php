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
        Schema::table('equipment', function (Blueprint $table) {
            // Ajouter la colonne location_id en tant que nullable d'abord
            $table->foreignId('location_id')
                  ->nullable()
                  ->after('location')
                  ->constrained('locations')
                  ->nullOnDelete();
        });

        // Mettre à jour les emplacements existants
        \DB::statement("UPDATE equipment SET location_id = (SELECT id FROM locations WHERE name = equipment.location LIMIT 1)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
};
