<?php

namespace Database\Factories;

use App\Models\ClassTag;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assay>
 */
class AssayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $teacher = Teacher::inRandomOrder()->first();
        return [
            'title' => $this->faker->sentence(),
            'deadline' => $this->faker->dateTimeBetween('now', '+1 week'),
            'is_visible' => $this->faker->boolean(),
            'teacher_id' => Teacher::inRandomOrder()->first()->id,
            'subject_id' => Subject::inRandomOrder()->first()->id,
            'class_id' => ClassTag::inRandomOrder()->first()->id,
        ];
    }
}
