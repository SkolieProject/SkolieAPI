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
        'asking',
        'correct_alternative'
    ];





    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class, 'question_id', 'id');
    }
}
