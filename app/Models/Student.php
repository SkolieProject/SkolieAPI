<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;
 

    protected $fillable = [
        'class_id'
    ];




    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, "student");
    }
}
