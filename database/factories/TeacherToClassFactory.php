<?php

namespace Database\Factories;

use App\Models\ClassTag;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeacherToClass>
 */
class TeacherToClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::inRandomOrder()->first()->id,
            'class_id' => ClassTag::inRandomOrder()->first()->id,
        ];
    }
}
