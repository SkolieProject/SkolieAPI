<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Answer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'assay_id'
    ];


    public function answer_template(): HasOne
    {
        return $this->hasOne(AnswerTemplate::class, 'answer_id', 'id');
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class, 'answer_id', 'id');
    }
}
