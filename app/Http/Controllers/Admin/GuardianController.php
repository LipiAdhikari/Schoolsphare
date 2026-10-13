<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuardianController extends Controller
{
    /**
     * Display a listing of guardians with search.
     */
    public function index(Request $request): View
    {
        $query = Guardian::with(['user', 'students']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $guardians = $query->orderBy('first_name')->paginate(15)->withQueryString();

        return view('admin.guardians.index', compact('guardians'));
    }

    /**
     * Show the form for creating a new guardian.
     */
    public function create(): View
    {
        $students = Student::orderBy('first_name')->get();
        return view('admin.guardians.create', compact('students'));
    }

    /**
     * Store a newly created guardian in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'relationship' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],

            // Optional portal account
            'create_user' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],

            // Optional student association
            'student_id' => ['nullable', 'exists:students,id'],
            'relation_type' => ['nullable', 'string', 'max:50'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = null;

            if ($request->boolean('create_user') && !empty($validated['email'])) {
                // Ensure email is not already taken
                $request->validate(['email' => ['unique:users,email']]);

                $user = User::create([
                    'name' => "{$validated['first_name']} {$validated['last_name']}",
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role' => 'parent',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            $guardian = Guardian::create([
                'user_id' => $user?->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'relationship' => $validated['relationship'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            if (!empty($validated['student_id'])) {
                $guardian->students()->attach($validated['student_id'], [
                    'relation_type' => $validated['relation_type'] ?? strtolower($validated['relationship']),
                    'is_primary' => $request->boolean('is_primary'),
                ]);
            }

            return redirect()->route('admin.guardians.show', $guardian)
                ->with('success', "Guardian profile for '{$guardian->full_name}' registered successfully.");
        });
    }

    /**
     * Display the specified guardian's profile.
     */
    public function show(Guardian $guardian): View
    {
        $guardian->load(['user', 'students.currentEnrollment.classSection.schoolClass']);
        $availableStudents = Student::whereNotIn('id', $guardian->students->pluck('id'))->orderBy('first_name')->get();

        return view('admin.guardians.show', compact('guardian', 'availableStudents'));
    }

    /**
     * Show the form for editing the guardian.
     */
    public function edit(Guardian $guardian): View
    {
        $guardian->load('user');
        return view('admin.guardians.edit', compact('guardian'));
    }

    /**
     * Update the specified guardian in storage.
     */
    public function update(Request $request, Guardian $guardian): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'relationship' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $guardian->update($validated);

        if ($guardian->user) {
            $guardian->user->update([
                'name' => "{$guardian->first_name} {$guardian->last_name}",
                'email' => $guardian->email ?? $guardian->user->email,
            ]);
        }

        return redirect()->route('admin.guardians.show', $guardian)
            ->with('success', "Guardian profile for '{$guardian->full_name}' updated successfully.");
    }

    /**
     * Link an additional student/ward to this guardian.
     */
    public function linkStudent(Request $request, Guardian $guardian): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'relation_type' => ['required', 'string', 'max:50'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        if ($guardian->students()->where('students.id', $validated['student_id'])->exists()) {
            return back()->with('warning', 'This student is already associated with this guardian.');
        }

        $guardian->students()->attach($validated['student_id'], [
            'relation_type' => $validated['relation_type'],
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('success', 'Student linked to guardian successfully.');
    }

    /**
     * Unlink a student/ward from this guardian.
     */
    public function unlinkStudent(Guardian $guardian, Student $student): RedirectResponse
    {
        $guardian->students()->detach($student->id);

        return back()->with('success', "Association with '{$student->full_name}' removed.");
    }
}
