<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeacherToClass extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'teacher_id',
        'class_id'
    ];


    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    public function class_tag(): HasOne
    {
        return $this->hasOne(ClassTag::class, 'id', 'class_tag_id');
    }

}
