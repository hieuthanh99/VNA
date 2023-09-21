<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\Task;
use App\Models\Logs;
use App\Models\ReportCenter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Models\Email;
use App\Mail\SendEmailUser;

class StockScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reports';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $endDate2 = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(16, 0, 0);

        $reportCenter = ReportCenter::whereBetween('created_at', [$startDate, $endDate2])->get();
        
        if(!$reportCenter->isEmpty()){
            Mail::to('n.hieuthanhps@gmail.com')->send(new SendEmailUser());

            \Log::info("Tổn tại báo cáo tuần ... !".$startDate);
            exit();
        }
       
        $records = Logs::whereBetween('created_at', [$startDate, $endDate])->get();
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

            $dateStart = Carbon::now();
            $jsonData = json_encode(array_values($dataByDepartment));
            $dataReportCenter = ReportCenter::create([
                'values' => $jsonData,
                'created_at' => $endDate2,
                'date_start' => $dateStart,
                'status' => 1,
            ]);

            $arrayCenter = json_decode($dataReportCenter->values);
            $departmentIds = []; 
            foreach ($arrayCenter as $item) {
                $departmentIds[] = $item->DepartmentId;
            }
            $dataStatusDepartment = [];
            $statusShow = 2;
            foreach ($departmentIds as $item) {
                $dataReport = Report::where('department_id', $item)->whereBetween('created_at', [$startDate, $endDate])->first();
                if ($dataReport) {
                    $status = $statusShow; 
                    $dataReport->status = $status; 
                    $dataReport->save(); 
                    $dataStatusDepartment[] = $dataReport;
                }
            }
            \Log::info("Testing Cron is Running ... !".$jsonData);
            $this->info('Daily report has been sent successfully!');
            $emailArray = Email::pluck('email')->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })->toArray();
                
            Mail::to($emailArray)->send(new SendEmailUser());
        }
        \Log::info("Not Fonnd Report ");
      
 
        exit();
      
    }
}
