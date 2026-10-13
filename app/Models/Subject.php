<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
    ];

    /**
     * Classes where this subject is taught.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
            ->withTimestamps();
    }

    /**
     * Teacher allocations for this subject.
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class, 'subject_id');
    }
}
