<?php

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
        Schema::create('assays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->datetime('deadline');
            $table->boolean('is_visible')->default(false);
            $table->foreignId('subject_id')->references('id')->on('subjects');
            $table->foreignId('teacher_id')->references('id')->on('teachers');
            $table->foreignId('class_id')->references('id')->on('class_tags');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assays');
    }
};
