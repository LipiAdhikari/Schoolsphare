<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeachingAssignmentController extends Controller
{
    /**
     * Display a listing of teacher allocations.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current()->first() ?? AcademicYear::latest()->first();

        $query = TeachingAssignment::with(['academicYear', 'teacher', 'classSection.schoolClass', 'subject']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        $assignments = $query->latest()->paginate(15)->withQueryString();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();
        $sections = ClassSection::with('schoolClass')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.assignments.index', compact(
            'assignments',
            'academicYears',
            'teachers',
            'sections',
            'subjects',
            'currentYear'
        ));
    }

    /**
     * Store a newly allocated teaching assignment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'class_section_id' => ['required', 'exists:class_sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        // Verify the user is actually a teacher
        $teacher = User::findOrFail($validated['teacher_id']);
        if (! $teacher->isTeacher()) {
            return redirect()->back()->withErrors(['teacher_id' => 'The selected user is not a teacher.']);
        }

        // Check for duplicate allocation
        $exists = TeachingAssignment::where('academic_year_id', $validated['academic_year_id'])
            ->where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'subject_id' => 'A teacher is already assigned to this subject in this section for the selected academic year.',
            ]);
        }

        TeachingAssignment::create($validated);

        return redirect()->route('admin.teaching-assignments.index')
            ->with('success', 'Teaching assignment created successfully.');
    }

    /**
     * Remove an existing teaching assignment.
     */
    public function destroy(TeachingAssignment $teachingAssignment): RedirectResponse
    {
        $teachingAssignment->delete();

        return redirect()->route('admin.teaching-assignments.index')
            ->with('success', 'Teaching assignment removed.');
    }
}
