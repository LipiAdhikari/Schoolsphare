<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of student enrollments with filters.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current()->first() ?? AcademicYear::latest()->first();

        $query = Enrollment::with(['academicYear', 'student', 'classSection.schoolClass']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20)->withQueryString();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $sections = ClassSection::with('schoolClass')->get();
        $students = User::where('role', 'student')->where('status', 'active')->orderBy('name')->get();

        return view('admin.enrollments.index', compact(
            'enrollments',
            'academicYears',
            'sections',
            'students',
            'currentYear'
        ));
    }

    /**
     * Store a newly created student enrollment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'student_id' => ['required', 'exists:users,id'],
            'class_section_id' => ['required', 'exists:class_sections,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:enrolled,promoted,repeated,transferred,withdrawn'],
        ]);

        $student = User::findOrFail($validated['student_id']);
        if (! $student->isStudent()) {
            return redirect()->back()->withErrors(['student_id' => 'The selected user is not registered as a student.']);
        }

        // Check if student is already enrolled for this academic year
        $alreadyEnrolled = Enrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->back()->withErrors([
                'student_id' => 'This student is already enrolled in a section for the selected academic year. Historical records must be preserved without duplicate active entries.',
            ]);
        }

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', "Student '{$student->name}' enrolled successfully.");
    }
}
