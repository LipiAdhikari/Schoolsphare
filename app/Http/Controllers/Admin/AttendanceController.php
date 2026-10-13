<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display recent attendance sessions and statistics.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current()->first();

        $query = AttendanceSession::with([
            'classSection.schoolClass',
            'subject',
            'recorder',
            'attendances',
        ]);

        if ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->input('date'));
        }

        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->input('class_section_id'));
        }

        $sessions = $query->orderByDesc('attendance_date')->paginate(15)->withQueryString();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();

        // Calculate quick summary metrics for today
        $today = now()->toDateString();
        $todaySessions = AttendanceSession::whereDate('attendance_date', $today)->with('attendances')->get();
        $totalPresentToday = $todaySessions->flatMap->attendances->where('status', 'present')->count();
        $totalAbsentToday = $todaySessions->flatMap->attendances->where('status', 'absent')->count();
        $totalMarkedToday = $todaySessions->flatMap->attendances->count();

        $todayRate = $totalMarkedToday > 0 ? round(($totalPresentToday / $totalMarkedToday) * 100, 1) : null;

        $stats = [
            'sessions_count' => $todaySessions->count(),
            'present_count' => $totalPresentToday,
            'absent_count' => $totalAbsentToday,
            'rate' => $todayRate,
        ];

        return view('admin.attendance.index', compact('sessions', 'classes', 'currentYear', 'stats'));
    }

    /**
     * Show the attendance entry / bulk marking sheet.
     */
    public function create(Request $request): View
    {
        $currentYear = AcademicYear::current()->first();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $selectedSection = null;
        $students = collect();
        $existingSession = null;
        $date = $request->input('date', now()->toDateString());
        $sectionId = $request->input('class_section_id');

        if ($sectionId) {
            $selectedSection = ClassSection::with('schoolClass')->findOrFail($sectionId);

            // Check if a daily attendance session already exists for this section and date
            if ($currentYear) {
                $existingSession = AttendanceSession::where('academic_year_id', $currentYear->id)
                    ->where('class_section_id', $selectedSection->id)
                    ->whereDate('attendance_date', $date)
                    ->where('type', 'daily')
                    ->with('attendances')
                    ->first();

                // Fetch enrolled students in this section for the current academic year
                $enrolledUserIds = Enrollment::where('class_section_id', $selectedSection->id)
                    ->where('academic_year_id', $currentYear->id)
                    ->where('status', 'enrolled')
                    ->pluck('student_id');

                $students = Student::whereIn('user_id', $enrolledUserIds)
                    ->orderBy('first_name')
                    ->get();
            }
        }

        return view('admin.attendance.create', compact(
            'classes',
            'selectedSection',
            'date',
            'students',
            'existingSession',
            'currentYear'
        ));
    }

    /**
     * Store or update bulk attendance records.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_section_id' => ['required', 'exists:class_sections,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'type' => ['required', 'in:daily,subject_wise'],
            'subject_id' => ['nullable', 'required_if:type,subject_wise', 'exists:subjects,id'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $currentYear = AcademicYear::current()->firstOrFail();

        return DB::transaction(function () use ($validated, $currentYear, $request) {
            // 1. Create or retrieve AttendanceSession (Unique constraint guarantees no duplicates)
            $session = AttendanceSession::where('academic_year_id', $currentYear->id)
                ->where('class_section_id', $validated['class_section_id'])
                ->whereDate('attendance_date', $validated['attendance_date'])
                ->where('type', $validated['type'])
                ->where('subject_id', $validated['subject_id'] ?? null)
                ->first();

            if (! $session) {
                $session = AttendanceSession::create([
                    'academic_year_id' => $currentYear->id,
                    'class_section_id' => $validated['class_section_id'],
                    'attendance_date' => $validated['attendance_date'],
                    'type' => $validated['type'],
                    'subject_id' => $validated['subject_id'] ?? null,
                    'recorded_by' => auth()->id(),
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            } else {
                $session->update([
                    'recorded_by' => auth()->id(),
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            }

            // 2. Upsert individual student attendance rows
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

            return redirect()->route('admin.attendance.show', $session)
                ->with('success', "Attendance saved: {$presentCount} Present, {$absentCount} Absent.");
        });
    }

    /**
     * Display a specific attendance session details and report.
     */
    public function show(AttendanceSession $attendance): View
    {
        $attendanceSession = $attendance;
        $attendanceSession->load([
            'classSection.schoolClass',
            'subject',
            'recorder',
            'attendances.student',
        ]);

        return view('admin.attendance.show', compact('attendanceSession'));
    }

    /**
     * Display individual student attendance profile history.
     */
    public function student(Student $student): View
    {
        $student->load(['user', 'attendances.session.classSection.schoolClass']);

        $attendances = Attendance::with(['session.classSection.schoolClass', 'session.subject'])
            ->where('student_id', $student->id)
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->orderByDesc('attendance_sessions.attendance_date')
            ->select('attendances.*')
            ->paginate(20);

        $totalMarked = $student->attendances->count();
        $totalPresent = $student->attendances->where('status', 'present')->count();
        $totalAbsent = $student->attendances->where('status', 'absent')->count();
        $totalLate = $student->attendances->where('status', 'late')->count();
        $totalExcused = $student->attendances->where('status', 'excused')->count();

        $rate = $totalMarked > 0 ? round((($totalPresent + $totalLate) / $totalMarked) * 100, 1) : 0.0;

        $stats = [
            'total' => $totalMarked,
            'present' => $totalPresent,
            'absent' => $totalAbsent,
            'late' => $totalLate,
            'excused' => $totalExcused,
            'rate' => $rate,
        ];

        return view('admin.attendance.student', compact('student', 'attendances', 'stats'));
    }
}
