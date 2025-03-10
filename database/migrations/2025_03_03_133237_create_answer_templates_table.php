<?php

use App\Models\Alternative;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answer_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Answer::class);
            $table->foreignIdFor(Question::class); // Correct alternative o question
            $table->foreignIdFor(Alternative::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_templates');
    }
};