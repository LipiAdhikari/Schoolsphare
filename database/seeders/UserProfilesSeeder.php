<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = AcademicYear::current()->first();
        $grade10A = ClassSection::whereHas('schoolClass', function ($q) {
            $q->where('numeric_level', 10);
        })->where('name', 'Section A')->first();

        $grade9A = ClassSection::whereHas('schoolClass', function ($q) {
            $q->where('numeric_level', 9);
        })->where('name', 'Section A')->first();

        // 1. Demo Student Profile (Alexander Hayes)
        $studentUser = User::where('email', 'student@schoolsphere.test')->first();
        $alexander = Student::updateOrCreate(
            ['admission_number' => 'STU-2026-0001'],
            [
                'user_id' => $studentUser?->id,
                'first_name' => 'Alexander',
                'last_name' => 'Hayes',
                'gender' => 'male',
                'date_of_birth' => '2010-05-14',
                'blood_group' => 'O+',
                'address' => '742 Evergreen Terrace, Springfield',
                'phone' => '+1 (555) 234-5678',
                'emergency_contact_phone' => '+1 (555) 987-6543',
                'admission_date' => '2026-04-01',
                'status' => 'active',
            ]
        );

        // 2. Demo Teacher Profile (Sarah Jenkins)
        $teacherUser = User::where('email', 'teacher@schoolsphere.test')->first();
        Teacher::updateOrCreate(
            ['employee_number' => 'TCH-2026-0001'],
            [
                'user_id' => $teacherUser?->id,
                'first_name' => 'Sarah',
                'last_name' => 'Jenkins',
                'gender' => 'female',
                'email' => 'teacher@schoolsphere.test',
                'phone' => '+1 (555) 456-7890',
                'qualification' => 'M.Sc. Mathematics, B.Ed.',
                'specialization' => 'Pure Mathematics & Calculus',
                'joining_date' => '2023-08-15',
                'status' => 'active',
            ]
        );

        // 3. Demo Guardian Profile (Marcus Hayes)
        $parentUser = User::where('email', 'parent@schoolsphere.test')->first();
        $marcus = Guardian::updateOrCreate(
            ['phone' => '+1 (555) 987-6543'],
            [
                'user_id' => $parentUser?->id,
                'first_name' => 'Marcus',
                'last_name' => 'Hayes',
                'relationship' => 'Father',
                'email' => 'parent@schoolsphere.test',
                'occupation' => 'Senior Software Architect',
                'address' => '742 Evergreen Terrace, Springfield',
            ]
        );

        // Link Marcus to Alexander
        if ($alexander && $marcus) {
            $alexander->guardians()->syncWithoutDetaching([
                $marcus->id => [
                    'relation_type' => 'father',
                    'is_primary' => true,
                ],
            ]);
        }

        // 4. Additional Sample Student 1 (Emily Chen)
        $emilyUser = User::updateOrCreate(
            ['email' => 'emily.chen@schoolsphere.test'],
            [
                'name' => 'Emily Chen',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $emily = Student::updateOrCreate(
            ['admission_number' => 'STU-2026-0002'],
            [
                'user_id' => $emilyUser->id,
                'first_name' => 'Emily',
                'last_name' => 'Chen',
                'gender' => 'female',
                'date_of_birth' => '2011-09-22',
                'blood_group' => 'A+',
                'address' => '128 Willow Creek Lane, Springfield',
                'phone' => '+1 (555) 345-6789',
                'emergency_contact_phone' => '+1 (555) 876-5432',
                'admission_date' => '2026-04-01',
                'status' => 'active',
            ]
        );

        $chenMother = Guardian::updateOrCreate(
            ['phone' => '+1 (555) 876-5432'],
            [
                'first_name' => 'Mei',
                'last_name' => 'Chen',
                'relationship' => 'Mother',
                'email' => 'mei.chen@example.com',
                'occupation' => 'Biomedical Researcher',
                'address' => '128 Willow Creek Lane, Springfield',
            ]
        );
        $emily->guardians()->syncWithoutDetaching([
            $chenMother->id => ['relation_type' => 'mother', 'is_primary' => true],
        ]);

        if ($currentYear && $grade9A) {
            Enrollment::updateOrCreate(
                [
                    'student_id' => $emilyUser->id,
                    'academic_year_id' => $currentYear->id,
                ],
                [
                    'class_section_id' => $grade9A->id,
                    'roll_number' => '09A-01',
                    'status' => 'enrolled',
                ]
            );
        }

        // 5. Additional Sample Teacher (Dr. Robert Davis)
        $davisUser = User::updateOrCreate(
            ['email' => 'robert.davis@schoolsphere.test'],
            [
                'name' => 'Dr. Robert Davis',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        Teacher::updateOrCreate(
            ['employee_number' => 'TCH-2026-0002'],
            [
                'user_id' => $davisUser->id,
                'first_name' => 'Robert',
                'last_name' => 'Davis',
                'gender' => 'male',
                'email' => 'robert.davis@schoolsphere.test',
                'phone' => '+1 (555) 567-8901',
                'qualification' => 'Ph.D. in Physics',
                'specialization' => 'Integrated Sciences & Physics',
                'joining_date' => '2024-01-10',
                'status' => 'active',
            ]
        );
    }
}
