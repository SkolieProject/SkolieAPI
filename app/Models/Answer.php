<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Answer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student',
        'assay'
    ];


    public function answer_template(): HasOne
    {
        return $this->hasOne(AnswerTemplate::class, "answer_id");
    }


    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class, "answer");
    }
}
