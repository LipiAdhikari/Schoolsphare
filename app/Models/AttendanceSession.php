<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'class_section_id',
        'attendance_date',
        'type',
        'subject_id',
        'recorded_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    /**
     * Academic year for which attendance is recorded.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Class section for which attendance is recorded.
     */
    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    /**
     * Subject taught (for subject-wise attendance).
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * User who recorded the attendance session.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Individual student attendance records.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Scope for a specific date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Scope for a specific class section.
     */
    public function scopeForSection(Builder $query, int $sectionId): Builder
    {
        return $query->where('class_section_id', $sectionId);
    }

    /**
     * Attendance percentage rate (present + late / total).
     */
    public function getAttendanceRateAttribute(): float
    {
        $total = $this->attendances->count();
        if ($total === 0) {
            return 0.0;
        }

        $presentAndLate = $this->attendances
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($presentAndLate / $total) * 100, 1);
    }
}
