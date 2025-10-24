<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'academic_year_id',
        'category',
        'full_name',
        'nick_name',
        'gender',
        'city_born',
        'birthday',
        'hobby',
        'nisn',
        'nik',
        'number_akta_lahir',
        'number_kartu_keluarga',
        'address',
        'village',
        'district',
        'city',
        'province',
        'previous_school',
        'address_previous_school',
        'poscode',
        'father_nik',
        'father_name',
        'father_city_born',
        'father_birthday',
        'father_religion',
        'father_education',
        'father_relation',
        'father_job',
        'father_income',
        'father_phone',
        'mother_nik',
        'mother_name',
        'mother_city_born',
        'mother_birthday',
        'mother_religion',
        'mother_education',
        'mother_relation',
        'mother_job',
        'mother_income',
        'mother_phone',
        'date_received',
        'grade_received',
        'scan_akta_lahir',
        'scan_kartu_keluarga',
        'scan_ktp_ayah',
        'scan_ktp_ibu',
        'scan_nisn',
        'photo',
        'ttd_name',
        'ttd',
        'is_resign',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function letter()
    {
        return $this->hasOne(Letter::class);
    }

    public function scopeNotResign($query)
    {
        return $query->where('is_resign', false);
    }
}
