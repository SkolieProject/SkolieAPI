<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassTag extends Model
{
    use HasFactory;
 
    protected $fillable = ['tag'];



    public function teachers(): HasMany
    {
        return $this->HasMany(TeacherToClass::class, 'teacher');
    }
}
