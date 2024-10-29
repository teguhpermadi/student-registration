<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'year',
        'quota_regular',
        'quota_inklusi'
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }
}
