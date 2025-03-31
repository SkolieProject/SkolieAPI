<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assay extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'title',
        'teacher_id',
        'class_tag_id',
        'deadline',
        'subject_id',
        'is_visible',
        'is_answerable',
    ];


    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'id', 'question_id');
    }
}
