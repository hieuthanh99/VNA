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
        $reportDates = Logs::whereBetween('created_at', [$startDate, $endDate])->get();
        $reportData = Logs::all();
        $report = ReportCenter::whereBetween('updated_at', [$startDate, $endDate])->get();
        $departmentId = $request->input('departmentInput');
        // $dataReportCenter = ReportCenter::all();
        $dataReportCenter = ReportCenter::where('status', '1')->get();
        $departments = Report::where('department_id', $departmentId)->get();

        $departmentList = Department::all();
        $dataDepartment = [];
        $departmentReportDate = [];
        if(!empty($departmentId)) {
            foreach ($dataReportCenter as $reportCenter) {
                $value = json_decode($reportCenter->values, true);
                foreach ($value as $item) {
                    $id = $item['DepartmentId'];

                    if ($id == $departmentId) {
                       $dataDepartment[] = $item;
                       $createdDate = \Carbon\Carbon::parse($reportCenter->created_at);
                       $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                       $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                       $departmentReportDate[] ="Báo cáo tuần (Từ ngày " . $startDateOfWeek ." đến " .$endDateOfWeek. ")";
   
                       continue;
                    }
                }
                   
            }

            return view('dashboard', ['reportDates' => $reportDates ,'departmentReportDate' => $departmentReportDate ,'departmentId' => $departmentId ,'dataDepartment' => $dataDepartment ,'departmentList' => $departmentList ,'dataReportCenter' => $dataReportCenter,'reportData' => $reportData ,'reportCenter' => $report ,'reports' => []]);

        }
        
        return view('dashboard', ['reportDates' => $reportDates,'departmentList' => $departmentList ,'reportCenter' => $report ,'reports' => []]);
    }
}
