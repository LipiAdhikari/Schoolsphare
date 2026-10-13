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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('class_section_id')->constrained('class_sections')->cascadeOnDelete();
            $table->string('roll_number', 50)->nullable();
            $table->string('status', 20)->default('enrolled'); // enrolled, promoted, repeated, transferred, withdrawn
            $table->timestamps();

            // A student can only have one active enrollment record per academic year
            $table->unique(['student_id', 'academic_year_id']);
            $table->index(['class_section_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
