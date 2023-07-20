<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCenter;
use App\Models\Logs;
use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class ReportCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDateWeek = Carbon::now()->endOfWeek();
        $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(17, 0, 0);
        $department = Department::get()->toArray();;
        $data = ReportCenter::whereBetween('created_at', [$startDate, $endDate]);
        $idData = $data->value('id');
        $data = json_decode($data->value('values'), true) ?? [];
        $departmentId = $data[0]['DepartmentId'];

        $reportIds = DB::table('reports')
            ->select('id')
            ->where('department_id', $departmentId)
            ->pluck('id');

        $idReport = $reportIds[0];
        
        $mergedArray = [];
        $id = 0;
        foreach ($department as $dept) {
            $departmentId = $dept['id'];
        
            $item = array_filter($data, function ($value) use ($departmentId) {
                return $value['DepartmentId'] == $departmentId;
            });
        
            if (count($item) > 0) {
                $mergedArray[] = [
                    // 'IdReport' => 
                    'DepartmentId' => $item[$id]['DepartmentId'],
                    'DepartmentName' => $dept['name'],
                    'WorkDone' => $item[$id]['WorkDone'],
                    'ExpectedWork' => $item[$id]['ExpectedWork'],
                    'Request' => $item[$id]['Request']
                ];
            } else {
                $mergedArray[] = [
                    'DepartmentId' => $departmentId,
                    'DepartmentName' => $dept['name'],
                    'WorkDone' => [],
                    'ExpectedWork' => [],
                    'Request' => ''
                ];
            }
            $id++;
        }
        
        
        if (empty($data)) {
            return view('centers.index', ['id' => $idReport, 'data' => $data, 'startDate' => $startDate->format('d-m-Y'), 'endDate' => $endDateWeek->format('d-m-Y')]);
        }       
        return view('centers.index', ['id' => $idReport ,'data' => $mergedArray, 'startDate' => $startDate->format('d-m-Y'), 'endDate' => $endDateWeek->format('d-m-Y')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
