<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::withCount(['teachingAssignments', 'enrollments'])
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('admin.academic.index', compact('academicYears'));
    }

    /**
     * Store a newly created academic year.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'string', 'in:upcoming,active,closed'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'is_current' => $isCurrent,
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', "Academic session '{$validated['name']}' created successfully.");
    }

    /**
     * Set a specified academic year as the current active session.
     */
    public function setCurrent(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('is_current', true)->update(['is_current' => false]);

        $academicYear->update([
            'is_current' => true,
            'status' => 'active',
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', "Academic session '{$academicYear->name}' is now active.");
    }
}
