<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSection extends Model
{
    use HasFactory;

    protected $table = 'class_sections';

    protected $fillable = [
        'class_id',
        'name',
        'capacity',
        'room_number',
    ];

    /**
     * Parent class / grade level.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Teaching assignments allocated to this specific section.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class, 'class_section_id');
    }

    /**
     * Student enrollments assigned to this section.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_section_id');
    }

    /**
     * Full combined name (e.g. "Grade 10 — Section A").
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->schoolClass ? "{$this->schoolClass->name} — {$this->name}" : $this->name
        );
    }
}
