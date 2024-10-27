<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'full_name',
        'nick_name',
        'gender',
        'city_born',
        'birthday',
        'nisn',
        'nik',
        'adress',
        'village',
        'district',
        'city',
        'province',
        'previous_school',
        'address_previous_school',
        'poscode',
        'father_status',
        'father_nik',
        'father_name',
        'father_city_born',
        'father_birthday',
        'father_religon',
        'father_education',
        'father_relation',
        'father_job',
        'father_income',
        'father_phone',
        'mother_status',
        'mother_nik',
        'mother_name',
        'mother_city_born',
        'mother_birthday',
        'mother_religon',
        'mother_education',
        'mother_relation',
        'mother_job',
        'mother_income',
        'mother_phone',
        'date_received',
        'grade_received',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
