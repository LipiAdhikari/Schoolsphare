<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'remarks',
    ];

    /**
     * Parent attendance session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * Student whose attendance is recorded.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope for present students.
     */
    public function scopePresent(Builder $query): Builder
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope for absent students.
     */
    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope for late students.
     */
    public function scopeLate(Builder $query): Builder
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope for excused absences.
     */
    public function scopeExcused(Builder $query): Builder
    {
        return $query->where('status', 'excused');
    }
}
