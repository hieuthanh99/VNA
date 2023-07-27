<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDate extends Model
{
    use HasFactory;

    protected $table = 'report_dates';

    protected $fillable = ['report_date'];

    public $timestamps = true;
}
