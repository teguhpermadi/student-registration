<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'reference_number',
        'student_id',
        'academic_year_id',
        'file',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
