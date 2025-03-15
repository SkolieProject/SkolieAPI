<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;
 

    protected $fillable = [
        'user_id',
        'class_tag_id',
    ];


    public function class_tag(): HasOne
    {
        return $this->hasOne(ClassTag::class, 'id', 'class_tag_id');
    }

    public function answer(): HasMany
    {
        return $this->hasMany(Answer::class, 'id', "answer_id");
    }
}
