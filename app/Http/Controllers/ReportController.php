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
use Illuminate\Support\Facades\DB;
use App\Models\ReportDate;

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
        $report = Report::where('department_id', $department->id)->latest()->first();
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $dataWideReport = DB::table('center_wide_report')->whereBetween('created_at', [$startDate, $endDate])->first();
        $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDate, $endDate])->first();
        if($logs){
            $array = json_decode($logs->values, true);
            return view('reports.index', ['department' => $department, 'array' => $array, 'report' => $report, 'dataWideReport' => $dataWideReport]);
        }
        return view('reports.index', ['department' => $department, 'array' => null, 'report' => $report, 'dataWideReport' => $dataWideReport]);
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

        // $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->first();
        // if(isset($logs)){
        //     $values = json_decode($logs->values);
        //     $expectedWorkValues = $values->ExpectedWork;
        //     dd($values);
        //     return view('reports.create', ['department' => $department, 'expectedWorkValues' => $expectedWorkValues ]);
        // }
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
        try {
           // dd($request);
            // User and department
            $user = Auth::user();
            $department = Department::find($user->department);
            $requestData = $request->all();
            $reportDate = ReportDate::latest()->first();
            if(!empty($reportDate)) {
                $reportDate = $reportDate->report_date;
                $errorMessage = '';
    
                if (isset($requestData['end_date'])) {
                    $endDates = $requestData['end_date'];
                    foreach ($endDates as $endDate) {
    
                        $endDate = Carbon::parse($endDate);
                    
                        if ($endDate->lessThan($reportDate)) {
                            return redirect()->back()->with('error', 'Không thể chọn ngày kết thúc trong tuần đã chốt.');
                        } 
                    }
                }
    
                if(isset($requestData['end_date_tuan_toi'])) {
                    $endDatesTuanToi = $requestData['end_date_tuan_toi'];
                    foreach ($endDatesTuanToi as $endDateTuanToi) {
                        
                        $endDateTuanToi = Carbon::parse($endDateTuanToi);
                    
                        if ($endDateTuanToi->lessThan($reportDate)) {
                            return redirect()->back()->with(['error' => 'Không thể chọn ngày kết thúc trong tuần đã chốt.']);
                        }
                    }
                }
            }
            
            $mapDataDone = [];
            $mapDataNextWeek = [];

            $workDone = isset($requestData['cong_viec_da_lam']) ? $requestData['cong_viec_da_lam'] : null;
            $workDoneValues = isset($requestData['cong_viec_da_lam_values']) ? $requestData['cong_viec_da_lam_values'] : null;
            $startDate = isset($requestData['start_date']) ? $requestData['start_date'] : null;
            $endDate = isset($requestData['end_date']) ? $requestData['end_date'] : null;
            $statusWork = isset($requestData['trangthai_congviec']) ? $requestData['trangthai_congviec'] : null;
            $noteWork = isset($requestData['noi_dung_cong_viec']) ? $requestData['noi_dung_cong_viec'] : null;

            $nextWeekWork = isset($requestData['cong_viec_tuan_toi']) ? $requestData['cong_viec_tuan_toi'] : null;
            $nextWeekStartDate = isset($requestData['start_date_tuan_toi']) ? $requestData['start_date_tuan_toi'] : null;
            $nextWeekEndDate = isset($requestData['end_date_tuan_toi']) ? $requestData['end_date_tuan_toi'] : null;
            $nextWeekStatusWork = isset($requestData['trangthai_congviec_tuan_toi']) ? $requestData['trangthai_congviec_tuan_toi'] : null;
            $nextWeekNoteWork = isset($requestData['noi_dung_cong_viec_tuan_toi']) ? $requestData['noi_dung_cong_viec_tuan_toi'] : null;
            
            $note = isset($requestData['kien_nghi']) ? $requestData['kien_nghi'] : null;

            if (isset($workDoneValues)) {
                foreach ($workDone as $index => $value) {
                    $mapDataDone[$index] = [
                        'work_done' => $value,
                        'value_of_work' => $workDoneValues[$index] ?? null,
                        'start_date' => $startDate[$index] ?? null,
                        'end_date' => $endDate[$index] ?? null,
                        'status_work' => $statusWork[$index] ?? null,
                        'description' => $noteWork[$index] ?? null,
                    ];
                }
            }
            if (isset($nextWeekWork)) {
                foreach ($nextWeekWork as $index => $value) {
                    $mapDataNextWeek[$index] = [
                        'next_work' => $value,
                        'next_start_date' => $nextWeekStartDate[$index] ?? null,
                        'next_end_date' => $nextWeekEndDate[$index] ?? null,
                        'next_status_work' => $nextWeekStatusWork[$index] ?? null,
                        'next_description' => $nextWeekNoteWork[$index] ?? null,
                    ];
                }
            }
            // Tạo chuỗi JSON
            $jsonData = json_encode([
                'WorkDone' => $mapDataDone,
                'ExpectedWork' => $mapDataNextWeek,
                'Request' => $note,
            ], JSON_PRETTY_PRINT);

            $jsonData = json_decode($jsonData);
        //  dd($jsonData);
            $currentDateTime = Carbon::now();

            $report = Report::create([
                'department_id' => $department->id,
                'user_id' => $user->id,
                'start_date' => $currentDateTime,
                'end_date' => $currentDateTime,
                'values' => json_encode($jsonData),
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
                        'start_date' => Carbon::parse($data->start_date),
                        'end_date' => Carbon::parse($data->end_date),
                        'description' => $data->description,
                        'work_status' => $data->status_work
                        
                    ]);
                }
            }

            if (isset($jsonData->ExpectedWork)) {
                foreach ($jsonData->ExpectedWork as $data) {
                    Task::create([
                        'report_id' => $report->id,
                        'title' => $data->next_work,
                        'reports_title' => 'ExpectedWork',
                        'start_date' => Carbon::parse($data->next_start_date),
                        'end_date' => Carbon::parse($data->next_end_date),
                        'description' => $data->next_description,
                        'work_status' => $data->next_status_work
                    ]);
                }
            }

            if (isset($jsonData->Request)) {
                Task::create([
                    'report_id' => $report->id,
                    'description' => $jsonData->Request,
                    'reports_title' => 'Request',
                    'title' => 'Request',
                ]);
            }

            return redirect()->route('reports.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
        } catch (QueryException $e) {
            \Log::info("Error ", $e);
        }
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
        $user = Auth::user();
        if($user->role == 'admin') {
            $report = Report::where('id', $id)->first();
            $itemData = Report::find($id);
            // dd($$itemData->id);
            $department = Department::find($report->department_id);
            $report = Report::where('id', $id)->first();
            
            $data = Task::where('report_id', $report->id)->get();
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
    
            $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDate, $endDate])->first();
    
            if($logs){
    
                $array = json_decode($logs->values, true);
                return view('reports.edit', ['department' => $department, 'array' => $array, 'report' => $report]);
            }
        } else {
            $itemData = Report::find($id);
            // dd($$itemData->id);
            $department = Department::find($user->department);
            $report = Report::where('id', $id)->first();
            
            $data = Task::where('report_id', $report->id)->get();
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
    
            $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDate, $endDate])->first();
    
            if($logs){
    
                $array = json_decode($logs->values, true);
                return view('reports.edit', ['department' => $department, 'array' => $array, 'report' => $report]);
            }
            return view('reports.edit', ['department' => $department, 'array' => null, 'report' => $report]);
        }
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
        try {
        // dd($request);
            // User and department
            $user = Auth::user();
            $department = Department::find($user->department);
            $requestData = $request->all();
            // dd($requestData);

            $report = Report::find($id);
            $task = Task::where('report_id', $id)->get();

            foreach ($task as $item) {
                if($item->reports_title == 'WorkDone') {
                    $workDone = isset($requestData['cong_viec_da_lam']) ? $requestData['cong_viec_da_lam'] : $item->title;
                    $workDoneValues = isset($requestData['cong_viec_da_lam_values']) ? $requestData['cong_viec_da_lam_values'] : $item->status;
                    $startDate = isset($requestData['start_date']) ? $requestData['start_date'] : $item->start_date;
                    $endDate = isset($requestData['end_date']) ? $requestData['end_date'] : $item->end_date;
                    $statusWork = isset($requestData['trangthai_congviec']) ? $requestData['trangthai_congviec'] : $item->work_status;
                    $noteWork = isset($requestData['noi_dung_cong_viec']) ? $requestData['noi_dung_cong_viec'] : $item->description;
                } 
                if ($item->reports_title == 'ExpectedWork') {
                    $nextWeekWork = isset($requestData['cong_viec_tuan_toi']) ? $requestData['cong_viec_tuan_toi'] : $item->title;
                    $nextWeekStartDate = isset($requestData['start_date_tuan_toi']) ? $requestData['start_date_tuan_toi'] : $item->start_date;
                    $nextWeekEndDate = isset($requestData['end_date_tuan_toi']) ? $requestData['end_date_tuan_toi'] : $item->end_date;
                    $nextWeekStatusWork = isset($requestData['trangthai_congviec_tuan_toi']) ? $requestData['trangthai_congviec_tuan_toi'] : $item->work_status;
                    $nextWeekNoteWork = isset($requestData['noi_dung_cong_viec_tuan_toi']) ? $requestData['noi_dung_cong_viec_tuan_toi'] : $item->description;
                }
                if ($item->reports_title == 'Request') {
                    $note = isset($requestData['kien_nghi']) ? $requestData['kien_nghi'] : 'Chưa báo cáo';
                }
            }

                if (isset($workDoneValues)) {
                    foreach ($workDone as $index => $value) {
                        $mapDataDone[$index] = [
                            'work_done' => $value,
                            'value_of_work' => $workDoneValues[$index] ?? null,
                            'start_date' => $startDate[$index] ?? null,
                            'end_date' => $endDate[$index] ?? null,
                            'status_work' => $statusWork[$index] ?? null,
                            'description' => $noteWork[$index] ?? null,
                        ];
                    }
                }

                if (isset($nextWeekWork)) {
                    foreach ($nextWeekWork as $index => $value) {
                        $mapDataNextWeek[$index] = [
                            'next_work' => $value,
                            'next_start_date' => $nextWeekStartDate[$index] ?? null,
                            'next_end_date' => $nextWeekEndDate[$index] ?? null,
                            'next_status_work' => $nextWeekStatusWork[$index] ?? null,
                            'next_description' => $nextWeekNoteWork[$index] ?? null,
                        ];
                    }
                }


                // Tạo chuỗi JSON
                $jsonData = json_encode([
                    'WorkDone' =>  isset($mapDataDone) ? $mapDataDone : null,
                    'ExpectedWork' =>  isset($nextWeekWork) ? $mapDataNextWeek : null,
                    'Request' => $note,
                ], JSON_PRETTY_PRINT);

                $jsonData = json_decode($jsonData);

                $currentDateTime = Carbon::now();

                $report = Report::find($id);
                $report->update($request->start_date);
                $report->update($request->end_date);
                $report->update([
                    'values' => json_encode($jsonData)
                ]);

                $dataLogId = Logs::where('report_id', $id)->first();

                $log = Logs::find($dataLogId)->first();

                $log->update([
                    'values' => json_encode($jsonData)
                ]);

                $tasks = Task::where('report_id', $id)->get();

                if (isset($jsonData->WorkDone)) {
                    foreach ($jsonData->WorkDone as $index => $data) {
                        // Kiểm tra xem bản ghi trong mảng $tasks có tồn tại không
                        if (isset($tasks[$index])) {
                            $task = $tasks[$index];

                            $task->update([
                                'title' => $data->work_done,
                                'status' => $data->value_of_work,
                                'start_date' => Carbon::parse($data->start_date),
                                'end_date' => Carbon::parse($data->end_date),
                                'description' => $data->description,
                                'work_status' => $data->status_work
                            ]);
                            $task->save();
                        }
                    }
                }

                if (isset($jsonData->ExpectedWork)) {
                    $tasks = Task::where('reports_title', 'ExpectedWork')
                    ->where('report_id', $id)
                    ->get();
                    foreach ($jsonData->ExpectedWork as $index => $data) {
                        if (isset($tasks[$index])) {
                            $task = $tasks[$index];
                            $task->update([
                                'title' => $data->next_work,
                                'start_date' => Carbon::parse($data->next_start_date),
                                'end_date' => Carbon::parse($data->next_end_date),
                                'description' => $data->next_description,
                                'work_status' => $data->next_status_work
                            ]);
                            $task->save();
                        }
                    }
                }

                if (isset($jsonData->Request)) {
                    $existingTask = Task::where('reports_title', 'Request')
                    ->where('report_id', $id)
                    ->first();
                        $request = $jsonData->Request;
                        if($existingTask) {
                            $existingTask->update([
                                'description' => $request,
                            ]);
                            $existingTask->save();
                        }
                }
                

                $user = Auth::user();
            if($user->role == "admin") {
                return redirect()->route('centers.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
            }
            return redirect()->route('reports.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
        } catch (QueryException $e) {
            \Log::info("Error ", $e);
        }
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
