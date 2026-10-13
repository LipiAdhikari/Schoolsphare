<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects and class mappings.
     */
    public function index(): View
    {
        $subjects = Subject::with('classes')
            ->orderBy('name')
            ->paginate(15);

        $classes = SchoolClass::orderBy('numeric_level')->get();

        return view('admin.subjects.index', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:30', 'unique:subjects,code'],
            'type' => ['required', 'string', 'in:theory,practical,composite'],
        ]);

        $subject = Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->name}' ({$subject->code}) registered successfully.");
    }

    /**
     * Assign a subject to a specific class.
     */
    public function assignToClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $schoolClass = SchoolClass::findOrFail($validated['class_id']);
        $subject = Subject::findOrFail($validated['subject_id']);

        if ($schoolClass->subjects()->where('subjects.id', $subject->id)->exists()) {
            return redirect()->route('admin.subjects.index')
                ->with('warning', "'{$subject->name}' is already assigned to {$schoolClass->name}.");
        }

        $schoolClass->subjects()->attach($subject->id);

        return redirect()->route('admin.subjects.index')
            ->with('success', "'{$subject->name}' attached to {$schoolClass->name}.");
    }
}
