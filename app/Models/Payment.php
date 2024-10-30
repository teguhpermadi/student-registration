<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'proof'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
