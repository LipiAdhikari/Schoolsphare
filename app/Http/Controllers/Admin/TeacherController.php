<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers with search and filtering.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current()->first();

        $query = Teacher::with([
            'user',
            'teachingAssignments' => function ($q) use ($currentYear) {
                if ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                      ->with(['classSection.schoolClass', 'subject']);
                }
            },
        ]);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $teachers = $query->orderBy('first_name')->paginate(15)->withQueryString();

        return view('admin.teachers.index', compact('teachers', 'currentYear'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): View
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['nullable', 'email', 'max:150', 'unique:teachers,email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'employee_number' => ['nullable', 'string', 'max:50', 'unique:teachers,employee_number'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'specialization' => ['nullable', 'string', 'max:150'],
            'joining_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive'],

            // Optional portal user account
            'create_user' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = null;

            if ($request->boolean('create_user') && !empty($validated['email'])) {
                $user = User::create([
                    'name' => "{$validated['first_name']} {$validated['last_name']}",
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role' => 'teacher',
                    'status' => $validated['status'],
                    'email_verified_at' => now(),
                ]);
            }

            $employeeNumber = $validated['employee_number'] ?? null;
            if (empty($employeeNumber)) {
                $yearPart = date('Y');
                $count = Teacher::count() + 1;
                $employeeNumber = sprintf('TCH-%s-%04d', $yearPart, $count);
            }

            $teacher = Teacher::create([
                'user_id' => $user?->id,
                'employee_number' => $employeeNumber,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'qualification' => $validated['qualification'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'joining_date' => $validated['joining_date'],
                'status' => $validated['status'],
            ]);

            return redirect()->route('admin.teachers.show', $teacher)
                ->with('success', "Teacher '{$teacher->full_name}' ({$teacher->employee_number}) registered successfully.");
        });
    }

    /**
     * Display the specified teacher's profile.
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load([
            'user',
            'teachingAssignments.classSection.schoolClass',
            'teachingAssignments.subject',
            'teachingAssignments.academicYear',
        ]);

        $currentYear = AcademicYear::current()->first();

        return view('admin.teachers.show', compact('teacher', 'currentYear'));
    }

    /**
     * Show the form for editing the teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['nullable', 'email', 'max:150', 'unique:teachers,email,' . $teacher->id],
            'phone' => ['required', 'string', 'max:30'],
            'employee_number' => ['required', 'string', 'max:50', 'unique:teachers,employee_number,' . $teacher->id],
            'qualification' => ['nullable', 'string', 'max:150'],
            'specialization' => ['nullable', 'string', 'max:150'],
            'joining_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $teacher->update($validated);

        if ($teacher->user) {
            $teacher->user->update([
                'name' => "{$teacher->first_name} {$teacher->last_name}",
                'status' => $teacher->status,
                'email' => $teacher->email ?? $teacher->user->email,
            ]);
        }

        return redirect()->route('admin.teachers.show', $teacher)
            ->with('success', "Teacher profile for '{$teacher->full_name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status for a teacher.
     */
    public function toggleStatus(Teacher $teacher): RedirectResponse
    {
        $newStatus = $teacher->status === 'active' ? 'inactive' : 'active';
        $teacher->update(['status' => $newStatus]);

        if ($teacher->user) {
            $teacher->user->update(['status' => $newStatus]);
        }

        return back()->with('success', "Teacher status updated to '{$newStatus}'.");
    }
}
