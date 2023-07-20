<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\Task;
use App\Models\Logs;
use Illuminate\Support\Facades\Session;
use App\Models\ReportCenter;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $department = Department::get()->toArray();
        $user = Auth::user();
        if($user->role == 'admin'){
            $report = ReportCenter::all();
            return view('dashboard', ['reports' => $report, 'department' => $department, 'array' => []]);
        }else{
            $departmentUser = Department::find($user->department);
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
    
            $array = Logs::Where('department_id', $departmentUser->id)->whereBetween('created_at', [$startDate, $endDate])->first();
            return view('dashboard', ['array' => $array, 'department' => $department, 'reports' => []]);
        }     
    }
}
