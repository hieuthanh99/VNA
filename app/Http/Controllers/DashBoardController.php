<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\Task;
use App\Models\Email;
use App\Models\Logs;
use Illuminate\Support\Facades\Session;
use App\Models\ReportCenter;
use App\Mail\SendReportEmail;
use App\Mail\SendEmailUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    public function sendEmail(Request $request)
    {

        $emails = Email::pluck('email')->toArray();
        $dataCenter = ReportCenter::latest()->first();
        if($dataCenter) {
            Mail::to($emails)->send(new SendEmailUser());
            return redirect()->back()->with('success', 'Gửi email thành công.');
        } else {
            return redirect()->back()->with('error', 'Chưa có thông tin bản báo cáo, không gửi được email');
        }
    }

    public function copy($id)
    {
        $dataReportCopy = Logs::Where('id', $id)->first();
        $arrayCopy = json_decode($dataReportCopy->values, true);
        //Role
        $user = Auth::user();
        $department = Department::find($user->department);
        return view('reports.create', ['department' => $department,'arrayCopy' => $arrayCopy, 'expectedWorkValues' => null ]);
    }
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
            $departments = Department::all();
            return view('dashboard', ['reports' => $report, 'departments' => $departments, 'array' => []]);
        }else{
            $departmentUser = Department::find($user->department);
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $array = Logs::Where('department_id', $departmentUser->id)->get();
            return view('dashboard', ['array' => $array, 'department' => $department, 'reports' => []]);
        }
    }

    public function deleteDataWeek(){
        // Lấy ngày bắt đầu và kết thúc của tuần này
        // $startDate = Carbon::now()->startOfWeek();
        // $endDate = Carbon::now()->endOfWeek();

        $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        $today = Carbon::now();
        if($today > $thisThursdayFormatted)
        {
            $startDate = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
            $endDate = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
        } else {
            $startDate = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
            $endDate = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        }

        // Xóa dữ liệu trong bảng 'tasks' từ ngày bắt đầu đến ngày kết thúc của tuần này
        DB::table('tasks')->whereBetween('created_at', [$startDate, $endDate])->delete();

        // Xóa dữ liệu trong bảng 'logs' từ ngày bắt đầu đến ngày kết thúc của tuần này
        DB::table('logs')->whereBetween('created_at', [$startDate, $endDate])->delete();

        // Xóa dữ liệu trong bảng 'center_wide_report' từ ngày bắt đầu đến ngày kết thúc của tuần này
        DB::table('center_wide_report')->whereBetween('date_start', [$startDate, $endDate])->delete();

        // Xóa dữ liệu trong bảng 'reports' từ ngày bắt đầu đến ngày kết thúc của tuần này
        DB::table('reports')->whereBetween('created_at', [$startDate, $endDate])->delete();

        return redirect()->back()->with('success', 'Xóa dữ liệu tuần này thành công!');
    }
    public function search(Request $request)
    {
        $department = Department::get()->toArray();
        $user = Auth::user();
        $departmentUser = Department::find($user->department);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $array = Logs::Where('department_id', $departmentUser->id)->whereBetween('created_at', [$startDate, $endDate])->get();

        return view('dashboard', ['array' => $array, 'department' => $department, 'reports' => []]);
    }
    public function run()
    {
        try {

            $startDate = Carbon::now()->startOfWeek();
            $startDate->subDays(3);
            $dateStart = Carbon::now();
            $endDate = Carbon::now()->endOfWeek();
            $endDate2 = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 4)->setTime(16, 0, 0);


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

            $reportCenter = ReportCenter::whereBetween('date_start', [$lastFridayFormatted, $thisThursdayFormatted])->get();
            if(!$reportCenter->isEmpty()){
                return redirect()->back()->with('error', 'Bạn đã thực thi trong tuần này.');
            }

            $records = Logs::whereBetween('created_at', [$lastFridayFormatted, $thisThursdayFormatted])->get();
            if ($records->count() > 0) {
                $dataByDepartment = [];

                foreach ($records as $record) {
                    $values = json_decode($record->values, true);

                    $departmentId = $record->department_id;

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
                }
                $jsonData = json_encode(array_values($dataByDepartment));
                $dataReportCenter = ReportCenter::create([
                    'status' => 1,
                    'values' => $jsonData,
                    'created_at' => $endDate2,
                    'date_start' => $dateStart,
                ]);
                $arrayCenter = json_decode($dataReportCenter->values);
                $departmentIds = [];
                foreach ($arrayCenter as $item) {
                    $departmentIds[] = $item->DepartmentId;
                }
                $dataStatusDepartment = [];
                $statusShow = 2;
                foreach ($departmentIds as $item) {
                    $dataReport = Report::where('department_id', $item)->whereBetween('created_at', [$lastFridayFormatted, $thisThursdayFormatted])->first();
                    if ($dataReport) {
                        $status = $statusShow;
                        $dataReport->status = $status;
                        $dataReport->save();
                        $dataStatusDepartment[] = $dataReport;
                    }
                }

                \Log::info("Testing Cron is Running ... !".$jsonData);
                \Log::info('Daily report has been sent successfully!');
                //return 'Daily report has been sent successfully!';
                $pdfData = 'Đây là nội dung của PDF';
                $emailArray = User::pluck('email')->filter(function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                })->toArray();

                Mail::to($emailArray)->send(new SendReportEmail($pdfData));
                return redirect()->back()->with('success', 'Thực thi thành công báo cáo và email.');
            }
            \Log::info("Not Fonnd Report ");
            return redirect()->back()->with('error', 'Chưa có phòng ban nào báo cáo.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', $e);
        }
    }
}
