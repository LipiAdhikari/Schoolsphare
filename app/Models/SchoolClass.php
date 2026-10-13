<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'numeric_level',
    ];

    /**
     * Sections belonging to this class (e.g. Section A, Section B).
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ClassSection::class, 'class_id');
    }

    /**
     * Subjects taught in this class grade.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withTimestamps();
    }
}
