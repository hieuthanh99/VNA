<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'department_id',
        'report_id',
        'values',
    ];

    protected $casts = [
        'values' => 'json',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
