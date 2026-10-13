<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = AcademicYear::current()->first();
        if (!$currentYear) {
            return;
        }

        $teacherUser = User::where('email', 'teacher@schoolsphere.test')->first();
        $adminUser = User::where('email', 'admin@schoolsphere.test')->first();
        $recorderId = $teacherUser?->id ?? $adminUser?->id;

        // Grade 10 Section A
        $grade10A = ClassSection::whereHas('schoolClass', function ($q) {
            $q->where('numeric_level', 10);
        })->where('name', 'Section A')->first();

        // Alexander Hayes
        $alexander = Student::where('admission_number', 'STU-2026-0001')->first();

        if ($grade10A && $alexander && $recorderId) {
            // Seed past 5 weekdays of attendance
            $date = Carbon::today();
            $recordedDays = 0;

            while ($recordedDays < 5) {
                if (!$date->isWeekend()) {
                    $dateStr = $date->toDateString();

                    $session = AttendanceSession::updateOrCreate(
                        [
                            'academic_year_id' => $currentYear->id,
                            'class_section_id' => $grade10A->id,
                            'attendance_date' => $dateStr,
                            'type' => 'daily',
                            'subject_id' => null,
                        ],
                        [
                            'recorded_by' => $recorderId,
                            'remarks' => "Regular morning roll call.",
                        ]
                    );

                    // 4 days present, 1 day late
                    $status = ($recordedDays === 2) ? 'late' : 'present';
                    $remark = ($recordedDays === 2) ? 'Arrived 10 mins late due to transit' : null;

                    Attendance::updateOrCreate(
                        [
                            'attendance_session_id' => $session->id,
                            'student_id' => $alexander->id,
                        ],
                        [
                            'status' => $status,
                            'remarks' => $remark,
                        ]
                    );

                    $recordedDays++;
                }
                $date->subDay();
            }
        }
    }
}
