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
        Schema::create('equipment_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            
            // Origine du mouvement
            $table->string('origin_type')->nullable();
            $table->foreignId('origin_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('origin_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('origin_external')->nullable();
            $table->string('origin_contact')->nullable();
            
            // Destination du mouvement
            $table->string('destination_type')->nullable();
            $table->foreignId('destination_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('destination_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('destination_external')->nullable();
            $table->string('destination_contact')->nullable();
            
            // Détails du mouvement
            $table->enum('type', ['checkout', 'checkin', 'transfer', 'maintenance']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->dateTime('scheduled_date');
            $table->dateTime('completed_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            
            // Statut et approbation
            $table->string('status')->default('pending');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_movements');
    }
};
