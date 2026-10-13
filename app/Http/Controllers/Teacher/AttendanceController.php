<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\TeachingAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display listing of attendance sessions for teacher's assigned classes.
     */
    public function index(): View
    {
        $user = auth()->user();
        $currentYear = AcademicYear::current()->first();

        // Get class section IDs assigned to this teacher
        $assignedSectionIds = TeachingAssignment::where('teacher_id', $user->id)
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->pluck('class_section_id')
            ->unique();

        $sessions = AttendanceSession::with(['classSection.schoolClass', 'attendances'])
            ->whereIn('class_section_id', $assignedSectionIds)
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderByDesc('attendance_date')
            ->paginate(15);

        return view('teacher.attendance.index', compact('sessions', 'currentYear'));
    }

    /**
     * Show attendance recording sheet restricted to teacher's assigned sections.
     */
    public function create(Request $request): View
    {
        $user = auth()->user();
        $currentYear = AcademicYear::current()->first();

        // Only allow class sections assigned to this teacher
        $assignments = TeachingAssignment::with('classSection.schoolClass')
            ->where('teacher_id', $user->id)
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->get();

        $sections = $assignments->pluck('classSection')->unique('id');

        $selectedSection = null;
        $students = collect();
        $existingSession = null;
        $date = $request->input('date', now()->toDateString());
        $sectionId = $request->input('class_section_id');

        if ($sectionId) {
            // Enforce teacher authorization: teacher MUST be assigned to this section
            if (! $sections->contains('id', (int) $sectionId)) {
                abort(403, 'You are not assigned to teach this class section.');
            }

            $selectedSection = ClassSection::with('schoolClass')->findOrFail($sectionId);

            if ($currentYear) {
                $existingSession = AttendanceSession::where('academic_year_id', $currentYear->id)
                    ->where('class_section_id', $selectedSection->id)
                    ->whereDate('attendance_date', $date)
                    ->where('type', 'daily')
                    ->with('attendances')
                    ->first();

                $enrolledUserIds = Enrollment::where('class_section_id', $selectedSection->id)
                    ->where('academic_year_id', $currentYear->id)
                    ->where('status', 'enrolled')
                    ->pluck('student_id');

                $students = Student::whereIn('user_id', $enrolledUserIds)
                    ->orderBy('first_name')
                    ->get();
            }
        }

        return view('teacher.attendance.create', compact(
            'sections',
            'selectedSection',
            'date',
            'students',
            'existingSession',
            'currentYear'
        ));
    }

    /**
     * Store attendance submitted by teacher with authorization check.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $currentYear = AcademicYear::current()->firstOrFail();

        $validated = $request->validate([
            'class_section_id' => ['required', 'exists:class_sections,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        // Security check: teacher must be assigned to this section
        $isAssigned = TeachingAssignment::where('teacher_id', $user->id)
            ->where('class_section_id', $validated['class_section_id'])
            ->where('academic_year_id', $currentYear->id)
            ->exists();

        if (! $isAssigned) {
            abort(403, 'Unauthorized: You are not assigned to record attendance for this section.');
        }

        return DB::transaction(function () use ($validated, $currentYear, $user) {
            $session = AttendanceSession::where('academic_year_id', $currentYear->id)
                ->where('class_section_id', $validated['class_section_id'])
                ->whereDate('attendance_date', $validated['attendance_date'])
                ->where('type', 'daily')
                ->whereNull('subject_id')
                ->first();

            if (! $session) {
                $session = AttendanceSession::create([
                    'academic_year_id' => $currentYear->id,
                    'class_section_id' => $validated['class_section_id'],
                    'attendance_date' => $validated['attendance_date'],
                    'type' => 'daily',
                    'subject_id' => null,
                    'recorded_by' => $user->id,
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            } else {
                $session->update([
                    'recorded_by' => $user->id,
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            }

            $presentCount = 0;
            $absentCount = 0;

            foreach ($validated['attendances'] as $item) {
                Attendance::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id' => $item['student_id'],
                    ],
                    [
                        'status' => $item['status'],
                        'remarks' => $item['remarks'] ?? null,
                    ]
                );

                if ($item['status'] === 'present') {
                    $presentCount++;
                } elseif ($item['status'] === 'absent') {
                    $absentCount++;
                }
            }

            return redirect()->route('teacher.attendance.index')
                ->with('success', "Attendance saved for {$session->classSection->full_name}: {$presentCount} Present, {$absentCount} Absent.");
        });
    }
}
