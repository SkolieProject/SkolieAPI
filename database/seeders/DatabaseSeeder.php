<?php

namespace Database\Seeders;

use App\Models\ClassTag;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherToClass;
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

        $admin = User::factory()->create([
            'name' => 'Admin',
            'role' => 'ADMIN',
            'email' => 'admin@admin.com',
            'password' => 'adminpassword'
        ]);

        $math = Subject::factory()->create([
            'subject_name' => 'Matemática'
        ]);

        $filo = Subject::factory()->create([
            'subject_name' => 'Inglês'
        ]);
        
        $em3a = ClassTag::factory()->create([
            'tag' =>  'EM3A'
        ]);

        $em3b = ClassTag::factory()->create([
            'tag' =>  'EM3B'
        ]);
        

        $user_teacher_one = User::factory()->teacher()->create([
            'name' => 'Rosana Paiolo',
            'role' => 'TCHR',
            'email' => 'rosana.paiolo@example.com',
            'password' => 'password',
        ]);
        $teacher_one = Teacher::factory()->create([
            'user_id' => $user_teacher_one->id,
            'subject_id' => $math->id
        ]);

        $user_teacher_two = User::factory()->teacher()->create([
            'name' => 'Roy Sollon',
            'role' => 'TCHR',
            'email' => 'roy.sollon@example.com',
            'password' => 'password',
        ]);
        $teacher_two = Teacher::factory()->create([
            'user_id' => $user_teacher_two->id,  
            'subject_id' => $filo->id
        ]);


        TeacherToClass::factory()->create([
            'teacher_id' => $teacher_one->id,
            'class_tag_id' => $em3a->id
        ]);
        TeacherToClass::factory()->create([
            'teacher_id' => $teacher_two->id,
            'class_tag_id' => $em3a->id
        ]);
        

        Student::factory()->count(10)->create([
            'class_tag_id' => $em3a->id
        ]);
        Student::factory()->count(10)->create([
            'class_tag_id' => $em3b->id
        ]);
    }
}