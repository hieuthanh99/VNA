<?php

namespace App\Helpers;
use PDF;

use Illuminate\Http\Request;
use App\Models\ReportCenter;
use App\Models\Logs;
use Carbon\Carbon;
use App\Models\Department;

class Helper
{
    public static function reportWeeked()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDateWeek = Carbon::now()->endOfWeek();
        $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(17, 0, 0);
        $department = Department::get()->toArray();
        $data = ReportCenter::whereBetween('created_at', [$startDate, $endDate]);
        $record = $data->first();
       
        $data = json_decode($data->value('values'), true) ?? [];
        $mergedArray = [];
    
        foreach ($department as $dept) {
            $departmentId = $dept['id'];
           
            $item = array_filter($data, function ($value) use ($departmentId) {
                return $value['DepartmentId'] == $departmentId;
            });
        
            // Kiểm tra nếu mảng $item không rỗng
            if (!empty($item)) {
                foreach ($item as $itemData) {
                    $mergedArray[] = [
                        'DepartmentId' => $itemData['DepartmentId'],
                        'DepartmentName' => $dept['name'],
                        'WorkDone' => $itemData['WorkDone'],
                        'ExpectedWork' => $itemData['ExpectedWork'],
                        'Request' => $itemData['Request']
                    ];
                }
            } else {
                $mergedArray[] = [
                    'DepartmentId' => $departmentId,
                    'DepartmentName' => $dept['name'],
                    'WorkDone' => [],
                    'ExpectedWork' => [],
                    'Request' => ''
                ];
            }
        }
    
        // Trả về một mảng chứa các giá trị cần trả về
        return [
            'mergedArray' => $mergedArray,
            'startDate' => $startDate,
            'endDateWeek' => $endDateWeek,
            'record' => $record
        ];
    }

    public static function reportWeekedDetails($id)
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDateWeek = Carbon::now()->endOfWeek();
        $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(17, 0, 0);
        $department = Department::get()->toArray();
        $data = ReportCenter::Where('id', $id)->value('values');
        
        $data = json_decode($data, true) ?? [];
        $mergedArray = [];
    
        foreach ($department as $dept) {
            $departmentId = $dept['id'];
           
            $item = array_filter($data, function ($value) use ($departmentId) {
                return $value['DepartmentId'] == $departmentId;
            });
        
            // Kiểm tra nếu mảng $item không rỗng
            if (!empty($item)) {
                foreach ($item as $itemData) {
                    $mergedArray[] = [
                        'DepartmentId' => $itemData['DepartmentId'],
                        'DepartmentName' => $dept['name'],
                        'WorkDone' => $itemData['WorkDone'],
                        'ExpectedWork' => $itemData['ExpectedWork'],
                        'Request' => $itemData['Request']
                    ];
                }
            } else {
                $mergedArray[] = [
                    'DepartmentId' => $departmentId,
                    'DepartmentName' => $dept['name'],
                    'WorkDone' => [],
                    'ExpectedWork' => [],
                    'Request' => ''
                ];
            }
        }
    
        // Trả về một mảng chứa các giá trị cần trả về
        return [
            'mergedArray' => $mergedArray,
            'startDate' => $startDate,
            'endDateWeek' => $endDateWeek
        ];
    }
    
    public static function pdf()
    {
        $data = Helper::reportWeeked();
        //dd($data);
        $pdf = PDF::loadView('pdf.template',['department' => $data['mergedArray']]);
        
        return $pdf->download('report.pdf');
    }

    public static function pdfDetails($id)
    {
        $data = Helper::reportWeekedDetails($id);
        //dd($data);
        $pdf = PDF::loadView('pdf.template',['department' => $data['mergedArray']]);
        
        return $pdf->download('report.pdf');
    }
  
}