<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assay extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'teacher_id',
        'class_tag_id',
        'subject_id',
        'title',
        'is_visible',
        'is_answerable',
        'initial_date',
        'final_date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassTag::class, 'class_tag_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'assay_id', 'id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'assay_id', 'id');
    }
}
