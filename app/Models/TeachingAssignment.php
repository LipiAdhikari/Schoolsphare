<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'class_section_id',
        'subject_id',
    ];

    /**
     * Associated academic year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Assigned teacher (User account).
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Class section being taught.
     */
    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    /**
     * Subject being taught.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
