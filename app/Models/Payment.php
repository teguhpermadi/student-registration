<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'money',
        'proof',
        'for',
        'verified',
        'date_of_verifying',
    ];

    protected $casts = [
        'money' => MoneyCast::class,
        'for' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
