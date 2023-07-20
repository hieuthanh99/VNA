<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = ['report_id', 'title', 'reports_title', 'status', 'note', 'start_date', 'end_date'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
