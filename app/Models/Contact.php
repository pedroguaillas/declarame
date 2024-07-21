<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'identification',
        'name',
        'address',
        'special_contribution',
        'accounting',
        'retention_agent',
        'phantom_taxpayer',
        'no_transactions',
        'rimpe',
        'phone',
        'email',
    ];

    protected $casts = [
        'special_contribution' => 'boolean',
        'accounting' => 'boolean',
        'retention_agent' => 'boolean',
        'phantom_taxpayer' => 'boolean',
        'no_transactions' => 'boolean',
    ];
}
