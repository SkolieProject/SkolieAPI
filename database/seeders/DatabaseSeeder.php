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
        Subject::factory()->count(2)->create();
        ClassTag::factory()->count(2)->create();

        Teacher::factory()->count(2)->create();
        Student::factory()->count(20)->create();

    }
}