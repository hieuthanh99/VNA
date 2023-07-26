<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Department;

class UnitController extends Controller
{
    public function showUnits()
    {
        $department = Department::where('name', 'Ban Dịch vụ hành khách')->first();
        $departmentId = $department->id;
        $units = Unit::where('department_id', $departmentId)->get();
        return view('units.show', compact('units'));
    }
}
