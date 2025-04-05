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
        'class_tag_id'
    ];


    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'teacher_id', 'id');
    }

    public function class_tag(): HasOne
    {
        return $this->hasOne(ClassTag::class, 'class_tag_id', 'id');
    }

}
