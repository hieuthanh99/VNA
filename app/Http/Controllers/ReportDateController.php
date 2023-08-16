<?php

namespace App\Http\Controllers;

use App\Models\ReportDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;
use App\Models\ReportCenter;
use App\Models\Report;
use App\Models\Task;

class ReportDateController extends Controller
{
    public function index()
    {
        $reportDates = ReportDate::latest()->first();
        return view('reportdates.index', compact('reportDates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
        ]);

        $reportDate = $request->input('report_date');
        $selectedTime = $request->input('report_time');

        // Tính toán ngày báo cáo kết thúc sau 7 ngày
        // $endOfWeek = Carbon::createFromFormat('Y-m-d', $reportDate)->addDays(7)->toDateString();
        ReportDate::create([
            'report_date' => $reportDate,
            'report_time' => $selectedTime
        ]);

        return redirect()->route('report-dates.index', compact('reportDate'))->with('success', 'Ngày giờ báo cáo đã được đặt thành công.');
    }

    public function searchReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $startTime = \Carbon\Carbon::parse($startDate);
        $endTime = \Carbon\Carbon::parse($endDate);

        // $startDateOfWeekInput = $startTime->startOfWeek();
        // $endDateOfWeekInput = $endTime->endOfWeek();

        $dayOfWeek = $startTime->dayOfWeek;
        if ($dayOfWeek >= 5) {
            $startDateOfWeekInput = $startTime->copy()->subDays($dayOfWeek - 5);

        } else {
            $startDateOfWeekInput = $startTime->copy()->subDays($dayOfWeek + 6 - 4);
        }

        $dayOfWeek = $endTime->dayOfWeek;
        if ($dayOfWeek >= 5) {
            $endDateOfWeekInput = $endTime->copy()->addDays(4 - $dayOfWeek + 7);
        } else {
            $endDateOfWeekInput = $endTime->copy()->addDays(4 - $dayOfWeek);
        }

        $reportDates = Logs::whereBetween('created_at', [$startDate, $endDate])->get();
        $reportData = Logs::all();
        $report = ReportCenter::whereBetween('date_start', [$startDateOfWeekInput, $endDateOfWeekInput])->get();
        $departmentId = $request->input('departmentInput');
        $dataReportCenter = ReportCenter::where('status', '1')->get();
    
        $departmentList = Department::all();
        $dataDepartment = [];
        $departmentReportDate = [];
        if(!empty($departmentId) && empty($startDate) && empty($endDate)) {
            foreach ($dataReportCenter as $reportCenter) {
                $value = json_decode($reportCenter->values, true);
                foreach ($value as $item) {
                    $id = $item['DepartmentId'];
                    if ($id == $departmentId) {
                       $dataDepartment[] = $item;
                       $createdDate = \Carbon\Carbon::parse($reportCenter->date_start);
                        $dayOfWeek = $createdDate->dayOfWeek;
                        if ($dayOfWeek >= 5) {
                            $lastFriday = $createdDate->copy()->subDays($dayOfWeek - 5)->format('d-m-Y');
                            $thisThursday = $createdDate->copy()->addDays(4 - $dayOfWeek + 7)->format('d-m-Y');
                        } else {
                            $lastFriday = $createdDate->copy()->subDays($dayOfWeek + 6 - 4)->format('d-m-Y');
                            $thisThursday = $createdDate->copy()->addDays(4 - $dayOfWeek)->format('d-m-Y');
                        }
                       $departmentReportDate[] ="Báo cáo tuần (Từ ngày " . $lastFriday ." đến " .$thisThursday. ")";
   
                       continue;
                    }
                }
            }
            return view('dashboard', ['reportDates' => $reportDates ,'startDate' => $startDate,'endDate' => $endDate ,'departmentReportDate' => $departmentReportDate ,'departmentId' => $departmentId ,'dataDepartment' => $dataDepartment ,'departmentList' => $departmentList ,'dataReportCenter' => $dataReportCenter,'reportData' => $reportData ,'reportCenter' => $report ,'reports' => []]);
        }

        if(!empty($startDate) && !empty($endDate) && !empty($departmentId)) {
            $reportCen = ReportCenter::whereBetween('date_start', [$startDateOfWeekInput, $endDateOfWeekInput])
            ->where('status', '1')
            ->get()->all();
            $data = [];
            $name = '';
            if(!empty($reportCen)) {
                foreach($reportCen as  $reportWork) {
                    $resultLog = json_decode($reportWork->values, true);
                    $reportDataLog[] = $reportWork;
                    foreach ($resultLog as $item) {
                        $departmentIdLog = $item['DepartmentId'];
                        if ($departmentIdLog == $departmentId) {
                            $data[] = $item;
                            $departmentData = Department::find($departmentId);
                            $departmentName = $departmentData->name;    
                            $name = $departmentName;
    
                            continue;
                        }
                    }
                }
                return view('dashboard', ['reportDates' => $reportDates,
                'departmentList' => $departmentList ,
                'reportWork' => $reportWork ,
                'departmentName' => $name ,
                'departmentIdLog' => $departmentIdLog ,
                'departmentReportDate' => $departmentReportDate ,
                'resultLog' => $data ,
                'dataReportCenter' => $dataReportCenter,
                'departmentId' => $departmentId,
                'startDate' => $startDate,
                'endDate' => $endDate ,
                'reportDataLog' => $reportDataLog,
                'reportData' => $reportData ,'reports' => []]);
            }
            return view('dashboard', ['reportDates' => $reportDates,
            'departmentList' => $departmentList ,
            'departmentName' => $name ,
            'departmentReportDate' => $departmentReportDate ,
            'resultLog' => $data ,
            'dataReportCenter' => $dataReportCenter,
            'departmentId' => $departmentId,
            'startDate' => $startDate,
            'endDate' => $endDate ,
            'reportData' => $reportData ,'reports' => []]);
        }
        
        return view('dashboard', ['reportDates' => $reportDates,'startDate' => $startDate,'endDate' => $endDate ,'departmentList' => $departmentList ,'reportCenter' => $report ,'reports' => []]);
    }
}
