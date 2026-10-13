<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->parentUser = User::factory()->parent()->create();
    }

    /**
     * Test admin can access guardians directory.
     */
    public function test_admin_can_view_guardians_directory(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/guardians');
        $response->assertStatus(200);
        $response->assertSee('Guardians & Parents');
    }

    /**
     * Test non-admin cannot access guardians management.
     */
    public function test_unauthorized_roles_are_forbidden(): void
    {
        $this->actingAs($this->parentUser)->get('/admin/guardians')->assertStatus(403);
    }

    /**
     * Test registering a guardian and linking a student ward.
     */
    public function test_admin_can_register_guardian_and_link_student(): void
    {
        $student = Student::create([
            'admission_number' => 'STU-LINK-01',
            'first_name' => 'Henry',
            'last_name' => 'Cavill',
            'gender' => 'male',
            'date_of_birth' => '2012-04-10',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/guardians', [
            'first_name' => 'Colin',
            'last_name' => 'Cavill',
            'relationship' => 'Father',
            'phone' => '+1 (555) 888-9999',
            'email' => 'colin.cavill@example.com',
            'occupation' => 'Stockbroker',
            'address' => 'Jersey, Channel Islands',
            'student_id' => $student->id,
            'relation_type' => 'father',
            'is_primary' => '1',
            'create_user' => '1',
            'password' => 'secret1234',
        ]);

        $guardian = Guardian::where('phone', '+1 (555) 888-9999')->first();
        $this->assertNotNull($guardian);
        $response->assertRedirect("/admin/guardians/{$guardian->id}");

        $this->assertDatabaseHas('users', [
            'email' => 'colin.cavill@example.com',
            'role' => 'parent',
        ]);

        $this->assertDatabaseHas('student_guardians', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'relation_type' => 'father',
            'is_primary' => 1,
        ]);
    }

    /**
     * Test linking another student to an existing guardian.
     */
    public function test_admin_can_link_additional_student_to_guardian(): void
    {
        $guardian = Guardian::create([
            'first_name' => 'Martha',
            'last_name' => 'Kent',
            'relationship' => 'Mother',
            'phone' => '+1 (555) 321-7654',
        ]);

        $student = Student::create([
            'admission_number' => 'STU-KENT-01',
            'first_name' => 'Clark',
            'last_name' => 'Kent',
            'gender' => 'male',
            'date_of_birth' => '2011-06-18',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/guardians/{$guardian->id}/link-student", [
            'student_id' => $student->id,
            'relation_type' => 'adoptive_mother',
            'is_primary' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_guardians', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'relation_type' => 'adoptive_mother',
        ]);
    }

    /**
     * Test unlinking a student from a guardian.
     */
    public function test_admin_can_unlink_student(): void
    {
        $guardian = Guardian::create([
            'first_name' => 'Bruce',
            'last_name' => 'Wayne',
            'relationship' => 'Legal Guardian',
            'phone' => '+1 (555) 999-0001',
        ]);

        $student = Student::create([
            'admission_number' => 'STU-ROBIN-01',
            'first_name' => 'Dick',
            'last_name' => 'Grayson',
            'gender' => 'male',
            'date_of_birth' => '2012-03-20',
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $guardian->students()->attach($student->id, ['relation_type' => 'legal_guardian', 'is_primary' => true]);

        $response = $this->actingAs($this->admin)->delete("/admin/guardians/{$guardian->id}/students/{$student->id}");
        $response->assertRedirect();

        $this->assertDatabaseMissing('student_guardians', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
        ]);
    }
}
