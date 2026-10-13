<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->teacher = User::factory()->teacher()->create();
        $this->studentUser = User::factory()->create(['role' => 'student']);
    }

    /**
     * Test admin can access student directory and filter by status.
     */
    public function test_admin_can_view_student_directory(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/students');
        $response->assertStatus(200);
        $response->assertSee('Student Directory');
    }

    /**
     * Test teachers and students cannot access student administrative management.
     */
    public function test_unauthorized_roles_are_forbidden(): void
    {
        $this->actingAs($this->teacher)->get('/admin/students')->assertStatus(403);
        $this->actingAs($this->studentUser)->get('/admin/students/create')->assertStatus(403);
    }

    /**
     * Test admitting a student with user credentials, academic placement, and guardian.
     */
    public function test_admin_can_admit_student(): void
    {
        $year = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);
        $class = SchoolClass::create(['name' => 'Grade 10', 'numeric_level' => 10]);
        $section = ClassSection::create(['class_id' => $class->id, 'name' => 'Section A', 'capacity' => 35]);

        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'first_name' => 'Lucas',
            'last_name' => 'Montgomery',
            'gender' => 'male',
            'date_of_birth' => '2010-08-12',
            'admission_date' => '2026-04-01',
            'admission_number' => 'STU-2026-0099',
            'blood_group' => 'A+',
            'status' => 'active',
            'phone' => '+1 (555) 123-4567',
            'emergency_contact_phone' => '+1 (555) 765-4321',
            'create_user' => '1',
            'email' => 'lucas.montgomery@example.com',
            'password' => 'secret1234',
            'class_section_id' => $section->id,
            'roll_number' => '10A-15',
            'guardian_mode' => 'new',
            'guardian_first_name' => 'Arthur',
            'guardian_last_name' => 'Montgomery',
            'guardian_relationship' => 'Father',
            'guardian_phone' => '+1 (555) 765-4321',
        ]);

        $student = Student::where('admission_number', 'STU-2026-0099')->first();
        $this->assertNotNull($student);
        $response->assertRedirect("/admin/students/{$student->id}");

        // Verify User Account
        $this->assertDatabaseHas('users', [
            'email' => 'lucas.montgomery@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        // Verify Student Profile
        $this->assertEquals('Lucas Montgomery', $student->full_name);
        $this->assertEquals('A+', $student->blood_group);

        // Verify Guardian Linked
        $this->assertCount(1, $student->guardians);
        $this->assertEquals('Arthur Montgomery', $student->guardians->first()->full_name);

        // Verify Enrollment
        $this->assertDatabaseHas('enrollments', [
            'academic_year_id' => $year->id,
            'class_section_id' => $section->id,
            'roll_number' => '10A-15',
        ]);
    }

    /**
     * Test duplicate admission number is rejected by validation.
     */
    public function test_duplicate_admission_number_is_rejected(): void
    {
        Student::create([
            'admission_number' => 'STU-DUPLICATE-01',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '2010-01-01',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'admission_number' => 'STU-DUPLICATE-01',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '2011-01-01',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('admission_number');
    }

    /**
     * Test editing a student's profile information.
     */
    public function test_admin_can_update_student_profile(): void
    {
        $student = Student::create([
            'admission_number' => 'STU-EDIT-01',
            'first_name' => 'Oliver',
            'last_name' => 'Twist',
            'gender' => 'male',
            'date_of_birth' => '2010-03-15',
            'admission_date' => '2026-04-01',
            'status' => 'active',
            'phone' => '+1 (555) 111-2222',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/students/{$student->id}", [
            'first_name' => 'Oliver',
            'last_name' => 'Brown',
            'gender' => 'male',
            'date_of_birth' => '2010-03-15',
            'admission_date' => '2026-04-01',
            'admission_number' => 'STU-EDIT-01',
            'blood_group' => 'B+',
            'status' => 'active',
            'phone' => '+1 (555) 999-8888',
        ]);

        $response->assertRedirect("/admin/students/{$student->id}");
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'last_name' => 'Brown',
            'blood_group' => 'B+',
            'phone' => '+1 (555) 999-8888',
        ]);
    }

    /**
     * Test toggling active/inactive status syncs both student profile and portal user.
     */
    public function test_admin_can_toggle_student_status(): void
    {
        $user = User::factory()->create(['status' => 'active', 'role' => 'student']);
        $student = Student::create([
            'user_id' => $user->id,
            'admission_number' => 'STU-STATUS-01',
            'first_name' => 'Deactivate',
            'last_name' => 'Me',
            'gender' => 'female',
            'date_of_birth' => '2010-07-20',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/students/{$student->id}/toggle-status");
        $response->assertRedirect();

        $student->refresh();
        $user->refresh();

        $this->assertEquals('inactive', $student->status);
        $this->assertEquals('inactive', $user->status);
    }
}
