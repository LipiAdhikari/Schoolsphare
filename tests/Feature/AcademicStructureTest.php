<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStructureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $student;
    protected User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->teacher = User::factory()->teacher()->create();
        $this->student = User::factory()->create(['role' => 'student']);
        $this->parent = User::factory()->parent()->create();
    }

    /**
     * Test that admin can access all academic structure management interfaces.
     */
    public function test_admin_can_view_all_academic_pages(): void
    {
        $routes = [
            '/admin/academic-years',
            '/admin/classes',
            '/admin/subjects',
            '/admin/teaching-assignments',
            '/admin/enrollments',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test that non-admin roles are forbidden (HTTP 403) from academic configuration.
     */
    public function test_non_admin_roles_are_forbidden(): void
    {
        $this->actingAs($this->teacher)->get('/admin/academic-years')->assertStatus(403);
        $this->actingAs($this->student)->get('/admin/classes')->assertStatus(403);
        $this->actingAs($this->parent)->get('/admin/subjects')->assertStatus(403);
    }

    /**
     * Test creating academic year and toggling the current active year.
     */
    public function test_admin_can_create_academic_year_and_toggle_current(): void
    {
        $response1 = $this->actingAs($this->admin)->post('/admin/academic-years', [
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => 1,
            'status' => 'active',
        ]);
        $response1->assertRedirect('/admin/academic-years');
        $this->assertDatabaseHas('academic_years', ['name' => '2025-2026', 'is_current' => 1]);

        $year1 = AcademicYear::where('name', '2025-2026')->first();

        $response2 = $this->actingAs($this->admin)->post('/admin/academic-years', [
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'status' => 'active',
        ]);
        $response2->assertRedirect('/admin/academic-years');
        $year2 = AcademicYear::where('name', '2026-2027')->first();

        // Switch active session to year 2 (via PATCH)
        $toggleResponse = $this->actingAs($this->admin)->patch("/admin/academic-years/{$year2->id}/set-current");
        $toggleResponse->assertRedirect('/admin/academic-years');

        $this->assertDatabaseHas('academic_years', ['id' => $year1->id, 'is_current' => 0]);
        $this->assertDatabaseHas('academic_years', ['id' => $year2->id, 'is_current' => 1]);
    }

    /**
     * Test creating a class and multiple sections.
     */
    public function test_admin_can_create_class_and_sections(): void
    {
        $classResponse = $this->actingAs($this->admin)->post('/admin/classes', [
            'name' => 'Grade 10',
            'numeric_level' => 10,
        ]);
        $classResponse->assertRedirect('/admin/classes');
        $this->assertDatabaseHas('classes', ['name' => 'Grade 10', 'numeric_level' => 10]);

        $class = SchoolClass::where('name', 'Grade 10')->first();

        $secResponse = $this->actingAs($this->admin)->post("/admin/classes/{$class->id}/sections", [
            'name' => 'Section A',
            'capacity' => 35,
            'room_number' => '301',
        ]);
        $secResponse->assertRedirect('/admin/classes');
        $this->assertDatabaseHas('class_sections', [
            'class_id' => $class->id,
            'name' => 'Section A',
            'room_number' => '301',
        ]);
    }

    /**
     * Test duplicate section name within the same class is rejected by validation.
     */
    public function test_duplicate_section_in_same_class_is_rejected(): void
    {
        $class = SchoolClass::create(['name' => 'Grade 9', 'numeric_level' => 9]);
        ClassSection::create(['class_id' => $class->id, 'name' => 'Section A', 'capacity' => 30]);

        $response = $this->actingAs($this->admin)->post("/admin/classes/{$class->id}/sections", [
            'name' => 'Section A',
            'capacity' => 40,
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test creating subjects and associating them with classes.
     */
    public function test_admin_can_create_subject_and_link_to_class(): void
    {
        $class = SchoolClass::create(['name' => 'Grade 8', 'numeric_level' => 8]);

        $subjectResponse = $this->actingAs($this->admin)->post('/admin/subjects', [
            'name' => 'Science',
            'code' => 'SCI-08',
            'type' => 'composite',
        ]);
        $subjectResponse->assertRedirect('/admin/subjects');
        $this->assertDatabaseHas('subjects', ['code' => 'SCI-08']);

        $subject = Subject::where('code', 'SCI-08')->first();

        $assignResponse = $this->actingAs($this->admin)->post('/admin/subjects/assign-class', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);
        $assignResponse->assertRedirect('/admin/subjects');
        $this->assertDatabaseHas('class_subjects', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);
    }

    /**
     * Test assigning a teacher to a section and preventing duplicate allocation.
     */
    public function test_teacher_assignment_and_duplicate_prevention(): void
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
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH-10', 'type' => 'theory']);

        // First assignment succeeds
        $response1 = $this->actingAs($this->admin)->post('/admin/teaching-assignments', [
            'academic_year_id' => $year->id,
            'teacher_id' => $this->teacher->id,
            'class_section_id' => $section->id,
            'subject_id' => $subject->id,
        ]);
        $response1->assertRedirect('/admin/teaching-assignments');
        $this->assertDatabaseHas('teaching_assignments', [
            'academic_year_id' => $year->id,
            'teacher_id' => $this->teacher->id,
            'class_section_id' => $section->id,
            'subject_id' => $subject->id,
        ]);

        // Second assignment with another teacher for the SAME section and subject fails
        $anotherTeacher = User::factory()->teacher()->create();
        $response2 = $this->actingAs($this->admin)->post('/admin/teaching-assignments', [
            'academic_year_id' => $year->id,
            'teacher_id' => $anotherTeacher->id,
            'class_section_id' => $section->id,
            'subject_id' => $subject->id,
        ]);
        $response2->assertSessionHasErrors('subject_id');

        // Admin can remove an assignment
        $assignment = TeachingAssignment::first();
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/teaching-assignments/{$assignment->id}");
        $deleteResponse->assertRedirect('/admin/teaching-assignments');
        $this->assertDatabaseMissing('teaching_assignments', ['id' => $assignment->id]);
    }

    /**
     * Test student enrollment and preventing duplicate enrollments within the same academic year.
     */
    public function test_student_enrollment_and_single_active_year_constraint(): void
    {
        $year = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);
        $class = SchoolClass::create(['name' => 'Grade 10', 'numeric_level' => 10]);
        $sectionA = ClassSection::create(['class_id' => $class->id, 'name' => 'Section A', 'capacity' => 35]);
        $sectionB = ClassSection::create(['class_id' => $class->id, 'name' => 'Section B', 'capacity' => 35]);

        // Enroll in Section A
        $response1 = $this->actingAs($this->admin)->post('/admin/enrollments', [
            'student_id' => $this->student->id,
            'academic_year_id' => $year->id,
            'class_section_id' => $sectionA->id,
            'roll_number' => '10A-01',
            'status' => 'enrolled',
        ]);
        $response1->assertRedirect('/admin/enrollments');
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'academic_year_id' => $year->id,
            'class_section_id' => $sectionA->id,
            'roll_number' => '10A-01',
        ]);

        // Attempt second enrollment in Section B within the same year should be rejected
        $response2 = $this->actingAs($this->admin)->post('/admin/enrollments', [
            'student_id' => $this->student->id,
            'academic_year_id' => $year->id,
            'class_section_id' => $sectionB->id,
            'roll_number' => '10B-02',
            'status' => 'enrolled',
        ]);
        $response2->assertSessionHasErrors('student_id');
    }
}
