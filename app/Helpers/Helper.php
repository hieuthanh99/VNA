<?php

namespace App\Helpers;
use PDF;

use Illuminate\Http\Request;
use App\Models\ReportCenter;
use App\Models\Logs;
use Carbon\Carbon;
use App\Models\Department;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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
  
    public static function word() {
        // Truyền dữ liệu vào để in ra bản Word
        $data = Helper::reportWeeked();
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        // Lặp qua mảng dữ liệu và thêm từng dòng vào tài liệu Word
        $data = $data['mergedArray'];
        foreach ($data as $item) {
            // Thêm thông tin về phòng
            $section->addText($item['DepartmentName'], ['size' => 16, 'bold' => true]);
            if(!empty($item['WorkDone'])) {

            
                // Thêm thông tin về công việc đã làm
                $section->addText("Công việc đã thực hiện:", ['size' => 16, 'bold' => true]);
                foreach ($item['WorkDone'] as $workDone) {
                    $section->addText("Công việc đã thực hiện: " . $workDone['work_done'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $workDone['description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $workDone['start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $workDone['end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $workDone['status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }

                 // Thêm thông tin về công việc dự kiến
                $section->addText("Công việc dự kiến:", ['size' => 16, 'bold' => true]);
                foreach ($item['ExpectedWork'] as $expectedWork) {
                    $section->addText("Tiêu đề: " . $expectedWork['next_work'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $expectedWork['next_description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $expectedWork['next_start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $expectedWork['next_end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $expectedWork['next_status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }
        
                $section->addText("Kiến nghị: " . $item['Request'], ['size' => 14, 'bold' => false]);
        
                // Thêm khoảng trắng giữa các phần
                $section->addTextBreak(1);
            } else {
                $section->addText("Công việc đã thực hiện: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addText("Công việc dự kiến: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addText("Kiến nghị: " . 'Không có dữ liệu', ['size' => 14, 'bold' => false]);
                $section->addTextBreak(1);
            }
        }

        // Save the document as a Word file
        $filePath = storage_path('app/public/report-word.docx');

        $phpWord->save($filePath);

        // Return the generated Word file for download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}