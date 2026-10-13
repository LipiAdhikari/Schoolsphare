<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of classes and their sections.
     */
    public function index(): View
    {
        $classes = SchoolClass::with(['sections', 'subjects'])
            ->withCount('sections')
            ->orderBy('numeric_level')
            ->paginate(15);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:classes,name'],
            'numeric_level' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $class = SchoolClass::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', "Class '{$class->name}' created successfully.");
    }

    /**
     * Store a new section for a specific class.
     */
    public function storeSection(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('class_sections', 'name')->where('class_id', $schoolClass->id),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
            'room_number' => ['nullable', 'string', 'max:50'],
        ], [
            'name.unique' => "Section with this name already exists in {$schoolClass->name}.",
        ]);

        $section = $schoolClass->sections()->create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', "{$section->name} successfully added to {$schoolClass->name}.");
    }
}
