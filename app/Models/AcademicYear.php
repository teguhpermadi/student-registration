<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'year',
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }
}
