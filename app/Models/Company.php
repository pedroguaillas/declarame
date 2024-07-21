<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        "team_id",
        "ruc",
        "name",
        "matrix_address",
        "special_contribution",
        "accounting",
        "retention_agent",
        "phantom_taxpayer",
        "no_transactions",
        "rimpe",
        "phone",
        "email",
        "type_declaration",
        "pass_sri",
    ];

    protected $casts = [
        "special_contribution" => "boolean",
        "accounting" => "boolean",
        "retention_agent" => "boolean",
        "phantom_taxpayer" => "boolean",
        "no_transactions" => "boolean",
    ];
}
