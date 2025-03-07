<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\Assay;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Assay::factory()
            ->count(3)
            ->has(Question::factory()
                ->count(10)
                ->has(Alternative::factory()
                    ->count(5)
                )
            )
            ->create();
    }
}
