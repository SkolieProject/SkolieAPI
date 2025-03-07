<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'user_id',
        'subject_name',
    ];



    public function user(): HasOne
    {
        return $this->hasOne(User::class, "user_id");
    }


    public function subject(): HasOne
    {
        return $this->hasOne(Subject::class, "subject_id");
    }

    
    public function classes(): HasMany
    {
        return $this->HasMany(TeacherToClass::class, 'class');
    }


    public function assays(): HasMany
    {
        return $this->hasMany(Assay::class, "teacher");
    }
}
