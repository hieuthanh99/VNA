<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentParent extends Model
{
    protected $table = 'department_parents';

    protected $fillable = [
        'name',
        'code',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class, 'department_parent_id', 'id');
    }
    
}
