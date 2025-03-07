<?php

namespace Database\Factories;

use App\Models\Alternative;
use App\Models\Assay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assay_id' => Assay::inRandomOrder()->first()->id() ?? Assay::factory(),
            'question_text' => $this->faker->sentence(),
            'correct_alternative' => Alternative::factory(),
        ];
    }
}
