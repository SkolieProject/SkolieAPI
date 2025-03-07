<?php

namespace Database\Seeders;

use App\Models\ClassTag;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $class = ClassTag::factory()->create([
            'tag' => 'EM3B',
        ]);

        $subject = Subject::factory()->create([
            'subject_name' => 'Maths',
        ]);

        $user_teacher = User::factory()->teacher()->create([
            'name' => 'João Felipe',
            'email' => 'joao.felipe@example.com',
            'password' => bcrypt('password'),
        ]);

        $user_student = User::factory()->student()->create([
            'name' => 'Miguel Andrade',
            'email' => 'miguel.andrade@example.com',
            'password' => bcrypt('password'),
        ]);

        $teacher = Teacher::factory()->create([
            'user_id' => $user_teacher->id,
            'subject_id' => $subject->id
        ]);

        $student = Student::factory()->create([
            'user_id' => $user_student->id,
            'class_id' => $class->id,
        ]);
    }
}