<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_section_id',
        'roll_number',
        'status',
    ];

    /**
     * Enrolled student (User account).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Academic year of the enrollment.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Class section where the student is enrolled.
     */
    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }
}
