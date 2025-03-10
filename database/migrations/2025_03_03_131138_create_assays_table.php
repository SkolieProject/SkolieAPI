<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\ClassTag;
use App\Models\Subject;
use App\Models\Teacher;

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
            $table->boolean('is_answerable')->default(false);
            $table->foreignIdFor(Subject::class);
            $table->foreignIdFor(Teacher::class);
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
