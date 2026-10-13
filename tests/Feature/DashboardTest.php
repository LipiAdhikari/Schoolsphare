<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * Test that the landing page renders successfully with SchoolSphere branding.
     */
    public function test_welcome_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SchoolSphere');
        $response->assertSee('Launch Dashboard');
    }

    /**
     * Test that the dashboard renders the master layout and executive components.
     */
    public function test_dashboard_renders_successfully_with_components(): void
    {
        $response = $this->get('/dashboard');

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
