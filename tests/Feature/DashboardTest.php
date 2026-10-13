<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the landing page renders successfully with SchoolSphere branding.
     */
    public function test_welcome_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SchoolSphere');
        $response->assertSee('Sign In to Portal');
    }

    /**
     * Test that guests cannot access the dashboard directly without logging in.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test that an authenticated user can view the dashboard and executive components.
     */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');
        $response->assertSee('Total Students');
        $response->assertSee('Teachers & Staff');
        $response->assertSee('Attendance Rate');
        $response->assertSee('Today\'s Section Attendance');
        $response->assertSee('Upcoming Examinations & Assessments');
        $response->assertSee('School Notices');
    }
}
