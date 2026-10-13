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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "Grade 10"
            $table->unsignedInteger('numeric_level'); // e.g. 10
            $table->timestamps();

            $table->index('numeric_level');
        });

        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name'); // e.g. "Section A"
            $table->unsignedInteger('capacity')->default(40);
            $table->string('room_number')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('classes');
    }
};
