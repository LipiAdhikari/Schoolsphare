<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $studentUser;
    protected AcademicYear $year;
    protected SchoolClass $class;
    protected ClassSection $sectionA;
    protected ClassSection $sectionB;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->teacher = User::factory()->teacher()->create();
        $this->studentUser = User::factory()->create(['role' => 'student']);

        $this->year = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        $this->class = SchoolClass::create(['name' => 'Grade 10', 'numeric_level' => 10]);
        $this->sectionA = ClassSection::create(['class_id' => $this->class->id, 'name' => 'Section A', 'capacity' => 35]);
        $this->sectionB = ClassSection::create(['class_id' => $this->class->id, 'name' => 'Section B', 'capacity' => 35]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'admission_number' => 'STU-TEST-01',
            'first_name' => 'Ethan',
            'last_name' => 'Hunt',
            'gender' => 'male',
            'date_of_birth' => '2010-06-15',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        Enrollment::create([
            'student_id' => $this->studentUser->id,
            'academic_year_id' => $this->year->id,
            'class_section_id' => $this->sectionA->id,
            'roll_number' => '10A-01',
            'status' => 'enrolled',
        ]);
    }

    /**
     * Test admin can view attendance registry and overview.
     */
    public function test_admin_can_view_attendance_registry(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/attendance');
        $response->assertStatus(200);
        $response->assertSee('Attendance Registry');
    }

    /**
     * Test admin can record bulk attendance for a class section.
     */
    public function test_admin_can_submit_bulk_attendance(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/attendance', [
            'class_section_id' => $this->sectionA->id,
            'attendance_date' => '2026-04-10',
            'type' => 'daily',
            'remarks' => 'Morning roll call test',
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present',
                    'remarks' => 'On time',
                ],
            ],
        ]);

        $session = AttendanceSession::where('class_section_id', $this->sectionA->id)
            ->whereDate('attendance_date', '2026-04-10')
            ->first();

        $this->assertNotNull($session);
        $response->assertRedirect("/admin/attendance/{$session->id}");

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
            'remarks' => 'On time',
        ]);
    }

    /**
     * Test duplicate attendance submission for same date & section is updated without duplicate rows.
     */
    public function test_re_submitting_attendance_updates_cleanly(): void
    {
        // First submission (present)
        $this->actingAs($this->admin)->post('/admin/attendance', [
            'class_section_id' => $this->sectionA->id,
            'attendance_date' => '2026-04-11',
            'type' => 'daily',
            'attendances' => [
                ['student_id' => $this->student->id, 'status' => 'present'],
            ],
        ]);

        // Second submission (correction to late)
        $this->actingAs($this->admin)->post('/admin/attendance', [
            'class_section_id' => $this->sectionA->id,
            'attendance_date' => '2026-04-11',
            'type' => 'daily',
            'attendances' => [
                ['student_id' => $this->student->id, 'status' => 'late', 'remarks' => 'Bus broke down'],
            ],
        ]);

        // Total attendance sessions for this date and section must be exactly 1
        $sessionCount = AttendanceSession::where('class_section_id', $this->sectionA->id)
            ->whereDate('attendance_date', '2026-04-11')
            ->count();
        $this->assertEquals(1, $sessionCount);

        // Student's attendance record should be updated to 'late'
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status' => 'late',
            'remarks' => 'Bus broke down',
        ]);
    }

    /**
     * Test admin can view attendance session details.
     */
    public function test_admin_can_view_session_sheet(): void
    {
        $session = AttendanceSession::create([
            'academic_year_id' => $this->year->id,
            'class_section_id' => $this->sectionA->id,
            'attendance_date' => '2026-04-12',
            'type' => 'daily',
            'recorded_by' => $this->admin->id,
        ]);

        Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/attendance/{$session->id}");
        $response->assertStatus(200);
        $response->assertSee('Ethan Hunt');
    }

    /**
     * Test admin can view student individual attendance transcript.
     */
    public function test_admin_can_view_student_attendance_history(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/students/{$this->student->id}/attendance");
        $response->assertStatus(200);
        $response->assertSee('Attendance Transcript');
        $response->assertSee('Ethan Hunt');
    }

    /**
     * Test teacher can record attendance for their assigned section.
     */
    public function test_teacher_can_record_attendance_for_assigned_section(): void
    {
        // Assign teacher to Section A
        TeachingAssignment::create([
            'academic_year_id' => $this->year->id,
            'teacher_id' => $this->teacher->id,
            'class_section_id' => $this->sectionA->id,
            'subject_id' => \App\Models\Subject::create(['name' => 'Math', 'code' => 'MTH-T1', 'type' => 'theory'])->id,
        ]);

        $response = $this->actingAs($this->teacher)->post('/teacher/attendance', [
            'class_section_id' => $this->sectionA->id,
            'attendance_date' => '2026-04-13',
            'attendances' => [
                ['student_id' => $this->student->id, 'status' => 'present'],
            ],
        ]);

        $response->assertRedirect('/teacher/attendance');
        $this->assertDatabaseHas('attendance_sessions', [
            'class_section_id' => $this->sectionA->id,
            'recorded_by' => $this->teacher->id,
        ]);
    }

    /**
     * Test teacher is forbidden from recording attendance for an unassigned section.
     */
    public function test_teacher_cannot_record_attendance_for_unassigned_section(): void
    {
        // Teacher is NOT assigned to Section B
        $response = $this->actingAs($this->teacher)->post('/teacher/attendance', [
            'class_section_id' => $this->sectionB->id,
            'attendance_date' => '2026-04-13',
            'attendances' => [
                ['student_id' => $this->student->id, 'status' => 'present'],
            ],
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test unauthorized roles cannot access admin attendance registry.
     */
    public function test_students_and_parents_are_forbidden(): void
    {
        $this->actingAs($this->studentUser)->get('/admin/attendance')->assertStatus(403);
        $parent = User::factory()->parent()->create();
        $this->actingAs($parent)->get('/admin/attendance')->assertStatus(403);
    }
}
