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

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $department = Department::find($user->department);
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDate, $endDate])->first();
        if($logs){
            $array = json_decode($logs->values, true);
            return view('reports.index', ['department' => $department, 'array' => $array]);
        }
        return view('reports.index', ['department' => $department, 'array' => null]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $startDate = Carbon::now()->startOfWeek();
        $nextMonday = $startDate->copy()->addWeek();
        // dd($nextMonday);
        // $endDate = Carbon::now()->endOfWeek();
        $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(16, 0, 0);
       
        $user = Auth::user();
        $department = Department::find($user->department);
        $expectedWorkValues = null;
        $reportCenter = ReportCenter::whereBetween('created_at', [$startDate, $endDate])->get();
       
        
        $existingRecord = Report::Where('department_id', $department->id)->whereBetween('created_at', [$startDate, $endDate]);
        if ($existingRecord->exists()) {
            return redirect()->route('reports.index')->with(['success' => 'Bạn đã tạo báo cáo vào ngày '.$existingRecord->first()->created_at->format('d-m-Y').'. Báo cáo tiếp theo vào ngày '.$nextMonday->format('d-m-Y')]);
        }
        else{
            if(!$reportCenter->isEmpty()){
                return redirect()->route('reports.index')->with(['error' => 'Phòng ban đã hết hạn báo cáo. Vui lòng báo cáo tiếp tục vào ngày '.$nextMonday->format('d-m-Y')]);
            }
        }
       
       
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        // dd($previousWeekStart);
        //Role
        $user = Auth::user();
        $department = Department::find($user->department);

        $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->first();
    //   dd($logs);
        if(isset($logs)){
            $values = json_decode($logs->values);
            $expectedWorkValues = $values->ExpectedWork;
            return view('reports.create', ['department' => $department, 'expectedWorkValues' => $expectedWorkValues ]);
        }
        return view('reports.create', ['department' => $department, 'expectedWorkValues' => null ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request);
        //User and department
        $user = Auth::user();
        $department = Department::find($user->department);
        $requestData = $request->all();

        $mapData = [];
        $workDone = isset($requestData['cong_viec_da_lam'])?$requestData['cong_viec_da_lam']:null;
        $workDoneValues = isset($requestData['cong_viec_da_lam_values'])?$requestData['cong_viec_da_lam_values']:null;
        $nextWeekWork = isset($requestData['cong_viec_tuan_toi'])?$requestData['cong_viec_tuan_toi']:null;
        $note = isset($requestData['kien_nghi'])?$requestData['kien_nghi']:null;
        if (isset($workDoneValues)) {
            foreach ($workDone as $index => $value) {
                $mapData[$index] = [
                    'work_done' => $value,
                    'value_of_work' => $workDoneValues[$index] ?? null,
                ];
            }
        }
        
        // Tạo chuỗi JSON
        $jsonData = json_encode([
            'WorkDone' => $mapData,
            'ExpectedWork' => $nextWeekWork,
            'Request' => $note,
        ], JSON_PRETTY_PRINT);
     
        $jsonData = json_decode($jsonData);
        $currentDateTime = Carbon::now();
        $report = Report::create([
            'department_id' => $department->id,
            'user_id' => $user->id,
            'start_date' => $currentDateTime,
            'end_date' => $currentDateTime,
            'status' => 1,
        ]);
        Logs::create([
            'department_id' => $department->id,
            'report_id' => $report->id,
            'values' => json_encode($jsonData)
        ]);
        if (isset($jsonData->WorkDone)) {
            foreach ($jsonData->WorkDone as $data) {
                Task::create([
                    'report_id' => $report->id,
                    'title' => $data->work_done,
                    'reports_title' => 'WorkDone',
                    'status' => $data->value_of_work,
                ]);
            }
        }
        if (isset($jsonData->ExpectedWork)) {
            foreach ($jsonData->ExpectedWork as $data) {
                Task::create([
                    'report_id' => $report->id,
                    'title' => $data,
                    'reports_title' => 'ExpectedWork',
                ]);
            }
        }
        if (isset($jsonData->Request)) {
            Task::create([
                'report_id' => $report->id,
                'title' => $jsonData->Request,
                'reports_title' => 'Request',
            ]);
        }   
        return redirect()->route('reports.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
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
