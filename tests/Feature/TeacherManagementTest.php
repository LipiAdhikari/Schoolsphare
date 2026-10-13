<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->teacherUser = User::factory()->teacher()->create();
    }

    /**
     * Test admin can access teachers directory.
     */
    public function test_admin_can_view_teachers_directory(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/teachers');
        $response->assertStatus(200);
        $response->assertSee('Teachers & Staff');
    }

    /**
     * Test non-admin cannot access teachers management.
     */
    public function test_unauthorized_roles_are_forbidden(): void
    {
        $this->actingAs($this->teacherUser)->get('/admin/teachers')->assertStatus(403);
    }

    /**
     * Test registering a new teacher with portal user account.
     */
    public function test_admin_can_register_teacher(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/teachers', [
            'first_name' => 'Claire',
            'last_name' => 'Redfield',
            'gender' => 'female',
            'employee_number' => 'TCH-2026-0045',
            'email' => 'claire.redfield@example.com',
            'phone' => '+1 (555) 777-8888',
            'qualification' => 'M.Ed. Science Education',
            'specialization' => 'Biology & Chemistry',
            'joining_date' => '2026-04-01',
            'status' => 'active',
            'create_user' => '1',
            'password' => 'secret1234',
        ]);

        $teacher = Teacher::where('employee_number', 'TCH-2026-0045')->first();
        $this->assertNotNull($teacher);
        $response->assertRedirect("/admin/teachers/{$teacher->id}");

        $this->assertDatabaseHas('users', [
            'email' => 'claire.redfield@example.com',
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $this->assertEquals('Claire Redfield', $teacher->full_name);
        $this->assertEquals('Biology & Chemistry', $teacher->specialization);
    }

    /**
     * Test duplicate employee number is rejected by validation.
     */
    public function test_duplicate_employee_number_rejected(): void
    {
        Teacher::create([
            'employee_number' => 'TCH-DUPLICATE-01',
            'first_name' => 'Original',
            'last_name' => 'Teacher',
            'gender' => 'male',
            'phone' => '+1 (555) 000-1111',
            'joining_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/teachers', [
            'employee_number' => 'TCH-DUPLICATE-01',
            'first_name' => 'Copycat',
            'last_name' => 'Teacher',
            'gender' => 'male',
            'phone' => '+1 (555) 222-3333',
            'joining_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('employee_number');
    }

    /**
     * Test updating a teacher profile.
     */
    public function test_admin_can_update_teacher(): void
    {
        $teacher = Teacher::create([
            'employee_number' => 'TCH-EDIT-01',
            'first_name' => 'Leon',
            'last_name' => 'Kennedy',
            'gender' => 'male',
            'phone' => '+1 (555) 333-4444',
            'joining_date' => '2025-09-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/teachers/{$teacher->id}", [
            'first_name' => 'Leon',
            'last_name' => 'Kennedy',
            'gender' => 'male',
            'employee_number' => 'TCH-EDIT-01',
            'phone' => '+1 (555) 999-0000',
            'qualification' => 'Ph.D. Physical Education',
            'specialization' => 'Athletics & Physical Science',
            'joining_date' => '2025-09-01',
            'status' => 'active',
        ]);

        $response->assertRedirect("/admin/teachers/{$teacher->id}");
        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'phone' => '+1 (555) 999-0000',
            'qualification' => 'Ph.D. Physical Education',
        ]);
    }

    /**
     * Test toggling active/inactive status syncs portal user.
     */
    public function test_admin_can_toggle_teacher_status(): void
    {
        $user = User::factory()->create(['status' => 'active', 'role' => 'teacher']);
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'employee_number' => 'TCH-STATUS-01',
            'first_name' => 'Jill',
            'last_name' => 'Valentine',
            'gender' => 'female',
            'phone' => '+1 (555) 444-5555',
            'joining_date' => '2025-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/teachers/{$teacher->id}/toggle-status");
        $response->assertRedirect();

        $teacher->refresh();
        $user->refresh();

        $this->assertEquals('inactive', $teacher->status);
        $this->assertEquals('inactive', $user->status);
    }
}
