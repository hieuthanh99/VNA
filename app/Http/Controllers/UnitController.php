<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DepartmentParent;

class UnitController extends Controller
{
    public function showUnits()
    {
        $departmentParent = DepartmentParent::where('code', 'DVHK')->first();
        $departmentParentId = $departmentParent->id;
        $departments = Department::where('department_parent_id', $departmentParentId)->get();
        return view('units.show', compact('departments'));
    }
}
