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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                $section->addText("Công việc đã thực hiện:", ['size' => 14, 'bold' => true]);
                foreach ($item['WorkDone'] as $workDone) {
                    $section->addText("Công việc đã thực hiện: " . $workDone['work_done'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $workDone['description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $workDone['start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $workDone['end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $workDone['status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }

                // Thêm khoảng trắng giữa các phần
                $section->addTextBreak(1);
            } else {
                $section->addText("Công việc đã thực hiện: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addTextBreak(1);
            }
            if(!empty($item['ExpectedWork'])) {

                // Thêm thông tin về công việc dự kiến
                $section->addText("Công việc dự kiến:", ['size' => 14, 'bold' => true]);
                foreach ($item['ExpectedWork'] as $expectedWork) {
                    $section->addText("Tiêu đề: " . $expectedWork['next_work'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $expectedWork['next_description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $expectedWork['next_start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $expectedWork['next_end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $expectedWork['next_status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }

                
            } else {
                $section->addText("Công việc dự kiến: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addTextBreak(1);
            }

            if(!empty($item['ExpectedWork'])) {
                $section->addText("Kiến nghị: " . $item['Request'], ['size' => 14, 'bold' => false]);
            } else {
                $section->addText("Kiến nghị: " . 'Không có dữ liệu', ['size' => 14, 'bold' => false]);
            }

        }

        // Save the document as a Word file
        $filePath = storage_path('app/public/report-word.docx');

        $phpWord->save($filePath);

        // Return the generated Word file for download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public static function generateWordDetail($id) {
        // Truyền dữ liệu vào để in ra bản Word
        $data = Helper::reportWeekedDetails($id);
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        // Lặp qua mảng dữ liệu và thêm từng dòng vào tài liệu Word
        $data = $data['mergedArray'];
        foreach ($data as $item) {
            // Thêm thông tin về phòng
            $section->addText($item['DepartmentName'], ['size' => 16, 'bold' => true]);
            if(!empty($item['WorkDone'])) {

            
                // Thêm thông tin về công việc đã làm
                $section->addText("Công việc đã thực hiện:", ['size' => 14, 'bold' => true]);
                foreach ($item['WorkDone'] as $workDone) {
                    $section->addText("Công việc đã thực hiện: " . $workDone['work_done'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $workDone['description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $workDone['start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $workDone['end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $workDone['status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }

                // Thêm khoảng trắng giữa các phần
                $section->addTextBreak(1);
            } else {
                $section->addText("Công việc đã thực hiện: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addTextBreak(1);
            }
            if(!empty($item['ExpectedWork'])) {

                // Thêm thông tin về công việc dự kiến
                $section->addText("Công việc dự kiến:", ['size' => 14, 'bold' => true]);
                foreach ($item['ExpectedWork'] as $expectedWork) {
                    $section->addText("Tiêu đề: " . $expectedWork['next_work'], ['size' => 14, 'bold' => false]);
                    $section->addText("Nội dung: " . $expectedWork['next_description'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày bắt đầu: " . $expectedWork['next_start_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Ngày kết thúc: " . $expectedWork['next_end_date'], ['size' => 14, 'bold' => false]);
                    $section->addText("Tiến độ: " . $expectedWork['next_status_work'], ['size' => 14, 'bold' => false]);
                    $section->addTextBreak(1);
                }

                
            } else {
                $section->addText("Công việc dự kiến: Không có dữ liệu", ['size' => 14, 'bold' => false]);
                $section->addTextBreak(1);
            }

            if(!empty($item['ExpectedWork'])) {
                $section->addText("Kiến nghị: " . $item['Request'], ['size' => 14, 'bold' => false]);
            } else {
                $section->addText("Kiến nghị: " . 'Không có dữ liệu', ['size' => 14, 'bold' => false]);
            }

        }

        // Save the document as a Word file
        $filePath = storage_path('app/public/report-word.docx');

        $phpWord->save($filePath);

        // Return the generated Word file for download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public static function excel() {
        $data = Helper::reportWeeked();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'Phòng ban');
        $sheet->setCellValue('B1', 'Công việc đã thực hiện');
        $sheet->setCellValue('C1', 'Ngày bắt đầu');
        $sheet->setCellValue('D1', 'Ngày kết thúc');
        $sheet->setCellValue('E1', 'Tiến độ');
        $sheet->setCellValue('F1', 'Nội dung');
        $sheet->setCellValue('G1', 'Công việc dự kiến');
        $sheet->setCellValue('H1', 'Ngày bắt đầu');
        $sheet->setCellValue('I1', 'Ngày kết thúc');
        $sheet->setCellValue('J1', 'Tiến độ');
        $sheet->setCellValue('K1', 'Nội dung');
        $sheet->setCellValue('L1', 'Kiến nghị');
    
        $row = 2;
        foreach ($data['mergedArray'] as $item) {
            $departmentName = $item['DepartmentName'];
            $sheet->setCellValue('A' . $row, $departmentName);
            $startRow = $row;
            $requestRow = $row;
            if (!empty($item['WorkDone'])) {
                foreach ($item['WorkDone'] as $workDone) {
                    $sheet->setCellValue('B' . $row, $workDone['work_done']);
                    $sheet->setCellValue('C' . $row, $workDone['start_date']);
                    $sheet->setCellValue('D' . $row, $workDone['end_date']);
                    $sheet->setCellValue('E' . $row, $workDone['status_work']);
                    $sheet->setCellValue('F' . $row, $workDone['description']);
                    $row++; 
                }
            }
        
            if (!empty($item['ExpectedWork'])) {
                foreach ($item['ExpectedWork'] as $expectedWork) {
                    $sheet->setCellValue('G' . $startRow, $expectedWork['next_work']);
                    $sheet->setCellValue('H' . $startRow, $expectedWork['next_start_date']);
                    $sheet->setCellValue('I' . $startRow, $expectedWork['next_end_date']);
                    $sheet->setCellValue('J' . $startRow, $expectedWork['next_status_work']);
                    $sheet->setCellValue('K' . $startRow, $expectedWork['next_description']);
                    $startRow++;
                }
            }
    
            $request = $item['Request'];
            $sheet->setCellValue('L' . $requestRow, $request);
        }
    
        $writer = new Xlsx($spreadsheet);
    
        $fileName = 'example.xlsx';
        $filePath = storage_path('app/public/') . $fileName;
        $writer->save($filePath);
    
        return response()->download($filePath)->deleteFileAfterSend();
    }

    public static function excelDetail($id) {
        $data = Helper::reportWeekedDetails($id);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'Phòng ban');
        $sheet->setCellValue('B1', 'Công việc đã thực hiện');
        $sheet->setCellValue('C1', 'Ngày bắt đầu');
        $sheet->setCellValue('D1', 'Ngày kết thúc');
        $sheet->setCellValue('E1', 'Tiến độ');
        $sheet->setCellValue('F1', 'Nội dung');
        $sheet->setCellValue('G1', 'Công việc dự kiến');
        $sheet->setCellValue('H1', 'Ngày bắt đầu');
        $sheet->setCellValue('I1', 'Ngày kết thúc');
        $sheet->setCellValue('J1', 'Tiến độ');
        $sheet->setCellValue('K1', 'Nội dung');
        $sheet->setCellValue('L1', 'Kiến nghị');
    
        $row = 2;
    
        foreach ($data['mergedArray'] as $item) {
            $departmentName = $item['DepartmentName'];
            $sheet->setCellValue('A' . $row, $departmentName);
            $startRow = $row;
            $requestRow = $row;
            if (!empty($item['WorkDone'])) {
                foreach ($item['WorkDone'] as $workDone) {
                    $sheet->setCellValue('B' . $row, $workDone['work_done']);
                    $sheet->setCellValue('C' . $row, $workDone['start_date']);
                    $sheet->setCellValue('D' . $row, $workDone['end_date']);
                    $sheet->setCellValue('E' . $row, $workDone['status_work']);
                    $sheet->setCellValue('F' . $row, $workDone['description']);
                    $row++;
                }
            }
        
            if (!empty($item['ExpectedWork'])) {
                foreach ($item['ExpectedWork'] as $expectedWork) {
                    $sheet->setCellValue('G' . $startRow, $expectedWork['next_work']);
                    $sheet->setCellValue('H' . $startRow, $expectedWork['next_start_date']);
                    $sheet->setCellValue('I' . $startRow, $expectedWork['next_end_date']);
                    $sheet->setCellValue('J' . $startRow, $expectedWork['next_status_work']);
                    $sheet->setCellValue('K' . $startRow, $expectedWork['next_description']);
                    $startRow++;
                }
            }
    
            $request = $item['Request'];
            $sheet->setCellValue('L' . $requestRow, $request);
        }
    
        $writer = new Xlsx($spreadsheet);
    
        $fileName = 'example.xlsx';
        $filePath = storage_path('app/public/') . $fileName;
        $writer->save($filePath);
    
        return response()->download($filePath)->deleteFileAfterSend();
    }
}