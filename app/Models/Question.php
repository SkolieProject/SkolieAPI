<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'assay_id',
        'number',
        'question_text',
        'correct_answer'
    ];





    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class, 'id', 'alternative_id');
    }
}
