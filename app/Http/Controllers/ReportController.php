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
        // $startDate = Carbon::now()->startOfWeek();
        // $endDate = Carbon::now()->endOfWeek();

        $thisFridayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        $today = Carbon::now();
        if($today > $thisFridayFormatted)
        {
            $startDate = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
            $endDate = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
        } else {
            $startDate = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
            $endDate = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        }

        $dataWideReport = DB::table('center_wide_report')->whereBetween('date_start', [$startDate, $endDate])->first();
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
        $endDate = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        $today = Carbon::now();

        if($today > $endDate)
        {
            $startDataSat = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
            $endFri = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
        } else {
            $startDataSat = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
            $endFri = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        }
        $user = Auth::user();
        $department = Department::find($user->department);
        $expectedWorkValues = null;
        $reportCenter = ReportCenter::whereBetween('created_at', [$startDataSat, $endFri])->get();

        $existingRecord = Report::Where('department_id', $department->id)->whereBetween('created_at', [$startDataSat, $endFri]);
        if ($existingRecord->exists()) {
            return redirect()->route('reports.index')->with(['success' => 'Bạn đã tạo báo cáo vào ngày '.$existingRecord->first()->created_at->format('d-m-Y').'. Báo cáo tiếp theo vào ngày '.$endFri->addDay()->format('d-m-Y')]);
        }
        else{
            if(!$reportCenter->isEmpty()){
                return redirect()->route('reports.index')->with(['error' => 'Phòng ban đã hết hạn báo cáo. Vui lòng báo cáo tiếp tục vào ngày '.$endFri->addDay()->format('d-m-Y')]);
            }
        }
        //Role
        $user = Auth::user();
        $department = Department::find($user->department);

        // $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$lastFridayFormatted, $thisThursdayFormatted])->first();
        // if(isset($logs)){
        //     $values = json_decode($logs->values);
        //     $expectedWorkValues = $values->ExpectedWork;
        //     return view('reports.index', ['department' => $department, 'expectedWorkValues' => $expectedWorkValues ]);
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
            if($user->role == 'admin') {
                $requestData = $request->all();
                $reportDate = ReportDate::latest()->first();
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

                $note = isset($requestData['kien_nghi']) ? $requestData['kien_nghi'] : 'Chưa báo cáo';

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
                    'Request' => isset($note) ? $note : 'Chưa báo cáo',
                ], JSON_PRETTY_PRINT);
                $jsonData = json_decode($jsonData);
                //dd($jsonData);
                $currentDateTime = Carbon::now();

                $report = Report::create([
                    'department_id' => $request->department_id,
                    'user_id' => $user->id,
                    'start_date' => $currentDateTime,
                    'end_date' => $currentDateTime,
                    'values' => json_encode($jsonData),
                    'status' => 2,
                ]);

                $log = Logs::create([
                    'department_id' => $request->department_id,
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

                // $startDate = Carbon::now()->startOfWeek();
                // $startDate->subDays(4);

                // $endDate2 = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 4)->setTime(16, 0, 0);
                $record = Logs::where('id', $log->id)->first();
                $values = json_decode($record->values, true);


                $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
                $today = Carbon::now();

                if($today > $thisThursdayFormatted)
                {
                    $lastFridayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(6);
                    $thisThursdayFormatted = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
                } else {
                    $lastFridayFormatted = Carbon::now()->startOfWeek()->subWeek()->addDays(5);
                    $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
                }

                $reportCenter = ReportCenter::whereBetween('date_start', [$lastFridayFormatted, $thisThursdayFormatted])->first();

                $departmentId = $record->department_id;
                $dataByDepartment = [];
                // Tạo một mảng mới cho phòng ban nếu chưa tồn tại
                if (!isset($dataByDepartment[$departmentId])) {
                    $departmentName = Department::find($departmentId);
                    $dataByDepartment[$departmentId] = [
                        'DepartmentId' => $departmentId,
                        'DepartmentName' => $departmentName->name,
                        'WorkDone' => [],
                        'ExpectedWork' => [],
                        'Request' => null,
                    ];
                }
                // Tổng hợp dữ liệu từ WorkDone
                if (isset($values['WorkDone'])) {
                    $dataByDepartment[$departmentId]['WorkDone'] = array_merge($dataByDepartment[$departmentId]['WorkDone'], $values['WorkDone']);
                }
                // Tổng hợp dữ liệu từ ExpectedWork
                if (isset($values['ExpectedWork'])) {
                    $dataByDepartment[$departmentId]['ExpectedWork'] = array_merge($dataByDepartment[$departmentId]['ExpectedWork'], $values['ExpectedWork']);
                }
                // Lưu giá trị từ Request (nếu có)
                if (isset($values['Request'])) {
                    $dataByDepartment[$departmentId]['Request'] = $values['Request'];
                }
                $jsonData = json_encode(array_values($dataByDepartment));
                $dataByDepartment = json_decode($jsonData);

                $reportCenterArray = json_decode($reportCenter->values);
                // $values = json_decode($dataByDepartment, true);
                $newValues = array_merge($reportCenterArray, $dataByDepartment);
                $reportCenter->values = json_encode($newValues);
                $reportCenter->save();

                return redirect()->route('centers.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
            } else {
                $department = Department::find($user->department);
                $requestData = $request->all();
                $reportDate = ReportDate::latest()->first();
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

                $note = isset($requestData['kien_nghi']) ? $requestData['kien_nghi'] : 'Chưa báo cáo';
                if (isset($workDone)) {
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
                    'Request' => isset($note) ? $note : 'Chưa báo cáo',
                ], JSON_PRETTY_PRINT);

                $jsonData = json_decode($jsonData);
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
            }
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
            $department = Department::find($report->department_id);
            $endDate = Carbon::now()->subWeek()->endOfWeek()->addDays(5);
            $today = Carbon::now();
            if($today > $endDate)
            {
                $startDataSat = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
                $endFri = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
            } else {
                $startDataSat = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
                $endFri = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
            }
            $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDataSat, $endFri])->first();

            if($logs){
                $array = json_decode($logs->values, true);
                return view('reports.edit', ['department' => $department, 'array' => $array, 'report' => $report]);
            }
        } else {
            $department = Department::find($user->department);
            $report = Report::where('id', $id)->first();
            $endDate = Carbon::now()->subWeek()->endOfWeek()->addDays(5);
            $today = Carbon::now();
            if($today > $endDate)
            {
                $startDataSat = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
                $endFri = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
            } else {
                $startDataSat = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
                $endFri = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
            }
            $logs = Logs::Where('department_id', $department->id)->whereBetween('created_at', [$startDataSat, $endFri])->first();
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
            // User and department
            $user = Auth::user();
            $department = Department::find($user->department);
            $requestData = $request->all();
            $report = Report::find($id);
            $tasks = Task::where('report_id', $id)->delete();

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

            $note = isset($requestData['kien_nghi']) ? $requestData['kien_nghi'] : 'Chưa báo cáo';
            if (isset($workDone)) {
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
                'Request' => isset($note) ? $note : 'Chưa báo cáo',
            ], JSON_PRETTY_PRINT);

            $jsonData = json_decode($jsonData);
            $currentDateTime = Carbon::now();
            $report = Report::find($id);
            $report->start_date = $currentDateTime;
            $report->end_date = $currentDateTime;
            $report->update([
                'values' => json_encode($jsonData)
            ]);

            $dataLogId = Logs::where('report_id', $id)->first();

            $log = Logs::find($dataLogId)->first();

            $log->update([
                'values' => json_encode($jsonData)
            ]);

            if (isset($jsonData->WorkDone)) {
                foreach ($jsonData->WorkDone as $data) {
                    Task::create([
                        'report_id' => $id,
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
                        'report_id' => $id,
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
                    'report_id' => $id,
                    'description' => $jsonData->Request,
                    'reports_title' => 'Request',
                    'title' => 'Request',
                ]);
            }
            // if (isset($jsonData->WorkDone)) {
            //     foreach ($jsonData->WorkDone as $index => $data) {
            //         // Kiểm tra xem bản ghi trong mảng $tasks có tồn tại không
            //         if (isset($tasks[$index])) {
            //             $task = $tasks[$index];

            //             $task->update([
            //                 'title' => $data->work_done,
            //                 'status' => $data->value_of_work,
            //                 'start_date' => Carbon::parse($data->start_date),
            //                 'end_date' => Carbon::parse($data->end_date),
            //                 'description' => $data->description,
            //                 'work_status' => $data->status_work
            //             ]);
            //             $task->save();
            //         }
            //     }
            // }

            // if (isset($jsonData->ExpectedWork)) {
            //     $tasks = Task::where('reports_title', 'ExpectedWork')
            //     ->where('report_id', $id)
            //     ->get();
            //     foreach ($jsonData->ExpectedWork as $index => $data) {
            //         if (isset($tasks[$index])) {
            //             $task = $tasks[$index];
            //             $task->update([
            //                 'title' => $data->next_work,
            //                 'start_date' => Carbon::parse($data->next_start_date),
            //                 'end_date' => Carbon::parse($data->next_end_date),
            //                 'description' => $data->next_description,
            //                 'work_status' => $data->next_status_work
            //             ]);
            //             $task->save();
            //         }
            //     }
            // }

            // if (isset($jsonData->Request)) {
            //     $existingTask = Task::where('reports_title', 'Request')
            //     ->where('report_id', $id)
            //     ->first();
            //         $request = $jsonData->Request;
            //         if($existingTask) {
            //             $existingTask->update([
            //                 'description' => $request,
            //             ]);
            //             $existingTask->save();
            //         }
            // }


            if($user->role == "admin") {
                $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
                $today = Carbon::now();

                if($today > $thisThursdayFormatted)
                {
                    $lastFridayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(6);
                    $thisThursdayFormatted = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
                } else {
                    $lastFridayFormatted = Carbon::now()->startOfWeek()->subWeek()->addDays(5);
                    $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
                }
                $reportCenter = ReportCenter::whereBetween('date_start', [$lastFridayFormatted, $thisThursdayFormatted])->first();
                $reportCenterArray = json_decode($reportCenter->values);
                foreach ($reportCenterArray as $item) {
                    if ($item->DepartmentId == $report->department_id) {
                        $item->WorkDone = $jsonData->WorkDone;
                        $item->ExpectedWork = $jsonData->ExpectedWork;
                        $item->Request = $jsonData->Request;
                    }
                }
                $reportCenterArray = json_encode($reportCenterArray);
                $reportCenter->values = $reportCenterArray;
                $reportCenter->save();
                return redirect()->route('centers.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
            } else {
                return redirect()->route('reports.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
            }
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
