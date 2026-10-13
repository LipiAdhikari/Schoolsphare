<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an administrator can access admin-only routes.
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that teachers cannot access admin routes (HTTP 403 Forbidden).
     */
    public function test_teacher_cannot_access_admin_routes(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that students cannot access admin routes (HTTP 403 Forbidden).
     */
    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that parents cannot access admin routes (HTTP 403 Forbidden).
     */
    public function test_parent_cannot_access_admin_routes(): void
    {
        $parent = User::factory()->parent()->create();

        $response = $this->actingAs($parent)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that a deactivated user is blocked and redirected to login with an error message.
     */
    public function test_inactive_user_is_logged_out_and_denied(): void
    {
        $inactiveUser = User::factory()->inactive()->create();

        $response = $this->actingAs($inactiveUser)->get('/dashboard');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test that public registration defaults to student role and rejects role spoofing.
     */
    public function test_public_registration_cannot_spoof_admin_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sneaky Visitor',
            'email' => 'visitor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // Malicious attempt to escalate role
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'visitor@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role, 'Public registration must default to student and reject role spoofing');
    }
}
