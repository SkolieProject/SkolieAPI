<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassTag extends Model
{
    use HasFactory;
 
    protected $fillable = ['tag'];



    public function teachers(): HasMany
    {
        return $this->HasMany(TeacherToClass::class, 'id', 'teacher_id');
    }

    public function assays(): HasMany
    {
        return $this->hasMany(Assay::class, 'id', 'assay_id');
    }
}
