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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->string('type')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['excellent', 'bon', 'moyen', 'mauvais', 'hors_service'])->default('bon');
            $table->string('location')->nullable();
            $table->boolean('is_usable')->default(true);
            $table->string('responsible_person')->nullable();
            $table->text('notes')->nullable();
            $table->text('suggestions')->nullable();
            $table->string('maintenance_frequency')->nullable();
            $table->string('maintenance_tasks')->nullable();
            $table->string('maintenance_type')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
