<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students with search and filtering.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current()->first();

        $query = Student::with([
            'user',
            'guardians',
            'enrollments' => function ($q) use ($currentYear) {
                if ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)->with('classSection.schoolClass');
                }
            },
        ]);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('class_section_id')) {
            $sectionId = $request->input('class_section_id');
            $query->whereHas('enrollments', function ($eq) use ($sectionId, $currentYear) {
                $eq->where('class_section_id', $sectionId);
                if ($currentYear) {
                    $eq->where('academic_year_id', $currentYear->id);
                }
            });
        }

        $students = $query->orderBy('first_name')->paginate(15)->withQueryString();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $sections = ClassSection::with('schoolClass')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes', 'sections', 'currentYear'));
    }

    /**
     * Show the form for admitting a new student.
     */
    public function create(): View
    {
        $currentYear = AcademicYear::current()->first();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $guardians = Guardian::orderBy('first_name')->get();

        return view('admin.students.create', compact('currentYear', 'classes', 'guardians'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'admission_date' => ['required', 'date'],
            'admission_number' => ['nullable', 'string', 'max:50', 'unique:students,admission_number'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive,graduated,transferred,suspended'],

            // Optional portal account
            'create_user' => ['nullable', 'boolean'],
            'email' => ['nullable', 'required_if:create_user,1', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],

            // Optional academic placement
            'class_section_id' => ['nullable', 'exists:class_sections,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],

            // Guardian linkage
            'guardian_mode' => ['nullable', 'in:existing,new,none'],
            'guardian_id' => ['nullable', 'required_if:guardian_mode,existing', 'exists:guardians,id'],
            'guardian_first_name' => ['nullable', 'required_if:guardian_mode,new', 'string', 'max:100'],
            'guardian_last_name' => ['nullable', 'required_if:guardian_mode,new', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'required_if:guardian_mode,new', 'string', 'max:30'],
            'guardian_relationship' => ['nullable', 'required_if:guardian_mode,new', 'string', 'max:50'],
            'guardian_email' => ['nullable', 'email', 'max:150'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = null;

            // 1. Create User account if requested
            if ($request->boolean('create_user') && !empty($validated['email'])) {
                $user = User::create([
                    'name' => "{$validated['first_name']} {$validated['last_name']}",
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role' => 'student',
                    'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
                    'email_verified_at' => now(),
                ]);
            }

            // 2. Auto-generate admission number if not provided
            $admissionNumber = $validated['admission_number'] ?? null;
            if (empty($admissionNumber)) {
                $yearPart = date('Y');
                $count = Student::count() + 1;
                $admissionNumber = sprintf('STU-%s-%04d', $yearPart, $count);
            }

            // 3. Create Student profile
            $student = Student::create([
                'user_id' => $user?->id,
                'admission_number' => $admissionNumber,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'blood_group' => $validated['blood_group'] ?? null,
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'admission_date' => $validated['admission_date'],
                'status' => $validated['status'],
            ]);

            // 4. Link Guardian
            if ($request->input('guardian_mode') === 'existing' && !empty($validated['guardian_id'])) {
                $student->guardians()->attach($validated['guardian_id'], [
                    'relation_type' => 'guardian',
                    'is_primary' => true,
                ]);
            } elseif ($request->input('guardian_mode') === 'new' && !empty($validated['guardian_first_name'])) {
                $guardian = Guardian::create([
                    'first_name' => $validated['guardian_first_name'],
                    'last_name' => $validated['guardian_last_name'],
                    'relationship' => $validated['guardian_relationship'] ?? 'Guardian',
                    'phone' => $validated['guardian_phone'],
                    'email' => $validated['guardian_email'] ?? null,
                ]);

                $student->guardians()->attach($guardian->id, [
                    'relation_type' => strtolower($validated['guardian_relationship'] ?? 'guardian'),
                    'is_primary' => true,
                ]);
            }

            // 5. Initial Session Enrollment if section provided
            if (!empty($validated['class_section_id']) && $user) {
                $currentYear = AcademicYear::current()->first();
                if ($currentYear) {
                    Enrollment::firstOrCreate(
                        [
                            'student_id' => $user->id,
                            'academic_year_id' => $currentYear->id,
                        ],
                        [
                            'class_section_id' => $validated['class_section_id'],
                            'roll_number' => $validated['roll_number'] ?? null,
                            'status' => 'enrolled',
                        ]
                    );
                }
            }

            return redirect()->route('admin.students.show', $student)
                ->with('success', "Student '{$student->full_name}' admitted successfully with Admission No. {$student->admission_number}.");
        });
    }

    /**
     * Display the specified student's profile.
     */
    public function show(Student $student): View
    {
        $student->load([
            'user',
            'guardians',
            'enrollments.classSection.schoolClass',
            'enrollments.academicYear',
        ]);

        $currentYear = AcademicYear::current()->first();

        return view('admin.students.show', compact('student', 'currentYear'));
    }

    /**
     * Show the form for editing the student.
     */
    public function edit(Student $student): View
    {
        $student->load(['user', 'guardians']);
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'admission_date' => ['required', 'date'],
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number,' . $student->id],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive,graduated,transferred,suspended'],
        ]);

        $student->update($validated);

        // Keep User status in sync if user account exists
        if ($student->user) {
            $student->user->update([
                'status' => $student->status === 'active' ? 'active' : 'inactive',
                'name' => "{$student->first_name} {$student->last_name}",
            ]);
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Student profile for '{$student->full_name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status for a student.
     */
    public function toggleStatus(Student $student): RedirectResponse
    {
        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        if ($student->user) {
            $student->user->update(['status' => $newStatus]);
        }

        return back()->with('success', "Student status updated to '{$newStatus}'.");
    }
}
