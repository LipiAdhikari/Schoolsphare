<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeachingAssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public landing portal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authenticated primary dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active'])
    ->name('dashboard');

// Administrator Routes (Protected with auth, active status, role:admin)
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Phase 4: Academic Structure
    Route::resource('academic-years', AcademicYearController::class)->only(['index', 'store']);
    Route::patch('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');

    Route::resource('classes', SchoolClassController::class)->only(['index', 'store']);
    Route::post('classes/{schoolClass}/sections', [SchoolClassController::class, 'storeSection'])->name('classes.sections.store');

    Route::resource('subjects', SubjectController::class)->only(['index', 'store']);
    Route::post('subjects/assign-class', [SubjectController::class, 'assignToClass'])->name('subjects.assign-class');

    Route::resource('teaching-assignments', TeachingAssignmentController::class)->only(['index', 'store', 'destroy']);

    Route::resource('enrollments', EnrollmentController::class)->only(['index', 'store']);

    // Phase 5: Student, Teacher, and Guardian Management
    Route::patch('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::resource('students', StudentController::class);

    Route::patch('teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
    Route::resource('teachers', TeacherController::class);

    Route::post('guardians/{guardian}/link-student', [GuardianController::class, 'linkStudent'])->name('guardians.link-student');
    Route::delete('guardians/{guardian}/students/{student}', [GuardianController::class, 'unlinkStudent'])->name('guardians.unlink-student');
    Route::resource('guardians', GuardianController::class);
});

// Teacher Routes
Route::middleware(['auth', 'active', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Student Routes
Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Parent Routes
Route::middleware(['auth', 'active', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Authenticated user profile routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication routes
require __DIR__.'/auth.php';
