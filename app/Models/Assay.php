<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assay extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'title',
        'teacher',
        'class',
        'is_visible',
        'is_answerable',
    ];


    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, "assay_id");
    }
}
