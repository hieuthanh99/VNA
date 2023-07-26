<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCenter extends Model
{
    use HasFactory;

    protected $table = 'center_wide_report';

    protected $fillable = [
        'values',
        'created_at',
        'status',
    ];

    protected $casts = [
        'values' => 'json',
    ];
}
