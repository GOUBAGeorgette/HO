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
            // D'abord ajouter la colonne purchase_cost si elle n'existe pas
            if (!Schema::hasColumn('equipment', 'purchase_cost')) {
                $table->decimal('purchase_cost', 10, 2)->nullable()->after('assigned_to');
            }
            
            // Ensuite ajouter les autres colonnes
            $table->integer('warranty_months')->nullable()->after('purchase_cost');
            $table->string('warranty_notes', 255)->nullable()->after('warranty_months');
            $table->date('warranty_expires')->nullable()->after('warranty_notes');
            $table->string('condition', 20)->nullable()->after('status');
            $table->string('supplier', 255)->nullable()->after('warranty_expires');
            $table->string('supplier_contact', 255)->nullable()->after('supplier');
            $table->string('order_number', 100)->nullable()->after('supplier_contact');
            $table->string('barcode', 100)->nullable()->after('order_number');
            $table->string('qr_code', 100)->nullable()->after('barcode');
            $table->integer('depreciation_years')->nullable()->after('qr_code');
            $table->decimal('residual_value', 10, 2)->nullable()->after('depreciation_years');
            $table->decimal('current_value', 10, 2)->nullable()->after('residual_value');
            $table->string('image_path', 255)->nullable()->after('current_value');
            
            // Ajout d'un index pour améliorer les performances sur les recherches
            $table->index('barcode');
            $table->index('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_cost',
                'warranty_months',
                'warranty_notes',
                'warranty_expires',
                'condition',
                'supplier',
                'supplier_contact',
                'order_number',
                'barcode',
                'qr_code',
                'depreciation_years',
                'residual_value',
                'current_value',
                'image_path'
            ]);
        });
    }
};
