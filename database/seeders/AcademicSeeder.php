<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Current Academic Year
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // Ensure other years are marked not current if any exist
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);

        // 2. Classes and Sections (Grade 8, 9, 10)
        $grades = [
            ['name' => 'Grade 8', 'numeric_level' => 8],
            ['name' => 'Grade 9', 'numeric_level' => 9],
            ['name' => 'Grade 10', 'numeric_level' => 10],
        ];

        $classes = [];
        $sections = [];

        foreach ($grades as $gradeData) {
            $class = SchoolClass::updateOrCreate(
                ['name' => $gradeData['name']],
                [
                    'numeric_level' => $gradeData['numeric_level'],
                ]
            );
            $classes[$gradeData['numeric_level']] = $class;

            // Sections A and B for each grade
            foreach (['Section A', 'Section B'] as $secIndex => $secName) {
                $room = ($gradeData['numeric_level'] * 10) + ($secIndex + 1);
                $section = ClassSection::updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'name' => $secName,
                    ],
                    [
                        'room_number' => (string) $room,
                        'capacity' => 35,
                    ]
                );
                $sections[$gradeData['numeric_level']][$secName] = $section;
            }
        }

        // 3. Core Subjects
        $subjectDefinitions = [
            ['name' => 'Mathematics', 'code' => 'MTH-101', 'type' => 'theory'],
            ['name' => 'Science', 'code' => 'SCI-101', 'type' => 'composite'],
            ['name' => 'English', 'code' => 'ENG-101', 'type' => 'theory'],
            ['name' => 'Social Studies', 'code' => 'SOC-101', 'type' => 'theory'],
        ];

        $subjects = [];
        foreach ($subjectDefinitions as $sDef) {
            $subject = Subject::updateOrCreate(
                ['code' => $sDef['code']],
                [
                    'name' => $sDef['name'],
                    'type' => $sDef['type'],
                ]
            );
            $subjects[$sDef['code']] = $subject;
        }

        // Attach all subjects to each class
        $subjectIds = collect($subjects)->pluck('id')->toArray();
        foreach ($classes as $class) {
            $class->subjects()->syncWithoutDetaching($subjectIds);
        }

        // 4. Teaching Assignment for Demo Teacher (Sarah Jenkins)
        $teacher = User::where('email', 'teacher@schoolsphere.test')->first();
        if ($teacher && isset($sections[10]['Section A']) && isset($subjects['MTH-101'])) {
            TeachingAssignment::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'class_section_id' => $sections[10]['Section A']->id,
                    'subject_id' => $subjects['MTH-101']->id,
                ],
                [
                    'teacher_id' => $teacher->id,
                ]
            );
        }

        // 5. Enrollment for Demo Student (Alexander Hayes)
        $student = User::where('email', 'student@schoolsphere.test')->first();
        if ($student && isset($sections[10]['Section A'])) {
            Enrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'class_section_id' => $sections[10]['Section A']->id,
                    'roll_number' => '10A-01',
                    'status' => 'enrolled',
                ]
            );
        }
    }
}
