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
use App\Models\Report;

class Helper
{
    public static function reportWeeked()
    {
        // $startDate = Carbon::now()->startOfWeek()->subWeek()->addDays(4);
        // $endDateWeek = Carbon::now()->endOfWeek()->subWeek()->addDays(4);
        // $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(17, 0, 0);
        $lastFridayFormatted = Carbon::now()->startOfWeek()->subWeek()->addDays(4);

        $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        $today = Carbon::now();
        if($today > $thisThursdayFormatted)
        {
            $startDate = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
            $endDateWeek = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
            $data = ReportCenter::whereBetween('date_start', [$startDate, $endDateWeek])->get();
        } else {
            $startDate = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
            $endDateWeek = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
            $data = ReportCenter::whereBetween('date_start', [$startDate, $endDateWeek])->get();
        }
        $department = Department::get()->toArray();
        if (!$data->isEmpty()) {
            $record = $data->first();
            $data = json_decode($data->value('values'), true) ?? [];
        } else {
            $record = ReportCenter::latest()->first();
            $values = $record->values;
            $data = json_decode($values, true) ?? [];
        }
        // $data = ReportCenter::whereBetween('created_at', [$startDate, $endDateWeek])->get();

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

    public static function reportItem()
    {
        // $startDate = Carbon::now()->startOfWeek();
        // $endDateWeek = Carbon::now()->endOfWeek();
        $startDate = Carbon::now()->startOfWeek()->subWeek()->addDays(4);

        $endDateWeek = Carbon::now()->endOfWeek()->subWeek()->addDays(4);
        $today = Carbon::now();
        $lastFridayFormatted = Carbon::now()->startOfWeek()->subWeek()->addDays(4);

        $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
        $today = Carbon::now();
        if($today > $thisThursdayFormatted)
        {
            $nowFriday = Carbon::now()->endOfWeek()->subWeek()->addDays(6)->startOfDay();
            $nowThursday = Carbon::now()->next()->endOfWeek()->subWeek()->addDays(5);
            $data = Report::whereBetween('created_at', [$nowFriday, $nowThursday])->get();
        } else {
            $lastFridayFormatted = Carbon::now()->startOfWeek()->subWeek()->addDays(5)->startOfDay();
            $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5);
            $data = Report::whereBetween('created_at', [$lastFridayFormatted, $thisThursdayFormatted])->get();
        }
        $endDate = Carbon::now()->setISODate(Carbon::now()->year, Carbon::now()->isoWeek(), 5)->setTime(17, 0, 0);
        $department = Department::get()->toArray();
        $arrayDepartmentIds = [];
        foreach ($data as $item) {
            $arrayDepartmentIds[] = $item->department_id;
        }

        $record = $data;

        $data = json_decode($data->value('values'), true) ?? [];
        $mergedArray = [];

        $departmentId = [];
        foreach ($department as $dept) {
            $departmentId[] = $dept['id'];
        }

        foreach ($departmentId as $id) {
            if (!in_array($id, $arrayDepartmentIds)) {
                $name = Department::find($id)->name;
                $mergedArray[] = [
                    'DepartmentId' => $id,
                    'DepartmentName' => $name,
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
        $dataCenter= ReportCenter::Where('id', $id)->first();

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
            'endDateWeek' => $endDateWeek,
            'record' => $dataCenter
        ];
    }

    public static function reportWeekedDepartment($id)
    {
        $data = Report::Where('id', $id)->first();
        $date = $data->start_date;
        $departmentId = Department::where('id', $data->department_id)->first();
        $deparmentName = $departmentId->name;

        $data = $data->values;
        $data = json_decode($data, true) ?? [];
        return [
            'data' => $data,
            'date' => $date,
            'deparmentName' => $deparmentName
        ];
    }

    public static function pdf()
    {
        $data = Helper::reportWeeked();
        $record = $data['record'];
        if(!empty($record)) {
            $dataDate =  $record->date_start;
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }
        }
        $pdf = PDF::loadView('pdf.template',['department' => $data['mergedArray'],'startDateOfWeekInput' => $startDateOfWeekInput, "endDateOfWeekInput" => $endDateOfWeekInput]);

        return $pdf->download('report.pdf');
    }

    public static function pdfDetails($id)
    {
        $data = Helper::reportWeekedDetails($id);
        $record = $data['record'];
        if(!empty($record)) {
            $dataDate =  $record->date_start;
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }
        }
        $pdf = PDF::loadView('pdf.template',['department' => $data['mergedArray'],'startDateOfWeekInput' => $startDateOfWeekInput, "endDateOfWeekInput" => $endDateOfWeekInput]);

        return $pdf->download('report.pdf');
    }

    public static function departmentPDF($id) {
        $data = Helper::reportWeekedDepartment($id);
        $record = $data['data'];
        if(!empty($record)) {
            $dataDate =  $data['date'];
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($dataDate)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }
        }
        $pdf = PDF::loadView('pdf.departmenttpl',['data' => $data['data'],'departmentName' => $data['deparmentName'],'startDateOfWeekInput' => $startDateOfWeekInput, "endDateOfWeekInput" => $endDateOfWeekInput]);

        return $pdf->download('report.pdf');
    }

    public static function word() {
        // Truyền dữ liệu vào để in ra bản Word
        $data = Helper::reportWeeked();
        $record = $data['record'];
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        // Lặp qua mảng dữ liệu và thêm từng dòng vào tài liệu Word
        if(!empty($record)) {
            $dataDate =  $record->date_start;
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }

            $section->addText("              BÁO CÁO CÔNG VIỆC TUẦN ($startDateOfWeekInput – $endDateOfWeekInput)", ['size' => 12, 'bold' => true]);
            $section->addTextBreak(3);
        }
        $data = $record;
        $data = json_decode($data->values);
        foreach ($data as $item) {
            // Thêm thông tin về phòng
            $section->addText($item->DepartmentName, ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
            $STTWorkDone = 1;
            $STTExpectedWork = 1;
            if(!empty($item->WorkDone)) {
                // Thêm thông tin về công việc đã làm
                $section->addText("I. Công việc đã thực hiện:", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
                foreach ($item->WorkDone as $workDone) {
                    $sttWorkDone = $STTWorkDone++;
                    $section->addText("$sttWorkDone. Công việc đã thực hiện: ", ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    $section->addText("- Tiêu đề : " . $workDone->work_done, ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    if($workDone->description) {
                        $section->addText("- Nội dung : " . $workDone->description, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone->start_date) {
                        $section->addText("- Ngày bắt đầu : " . $workDone->start_date, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone->end_date) {
                        $section->addText("- Ngày kết thúc : " . $workDone->end_date, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone->status_work) {
                        $section->addText("- Tiến độ : " . $workDone->status_work, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                }

                // Thêm khoảng trắng giữa các phần
                $section->addTextBreak(1);
            } else {
                $section->addText("I. Công việc đã thực hiện : Không có dữ liệu", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
            }
            if(!empty($item->ExpectedWork)) {

                // Thêm thông tin về công việc dự kiến
                $section->addText("II. Công việc dự kiến:", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
                foreach ($item->ExpectedWork as $expectedWork) {
                    $sttExpectedWork = $STTExpectedWork++;
                    $section->addText("$sttExpectedWork. Công việc dự kiến: ", ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    $section->addText("- Tiêu đề : " . $expectedWork->next_work, ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    if ($expectedWork->next_description) {
                        $section->addText("- Nội dung : " . $expectedWork->next_description, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if ($expectedWork->next_start_date) {
                        $section->addText("- Ngày bắt đầu : " . $expectedWork->next_start_date, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if ($expectedWork->next_end_date) {
                        $section->addText("- Ngày kết thúc : " . $expectedWork->next_end_date, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if ($expectedWork->next_status_work) {
                        $section->addText("- Tiến độ : " . $expectedWork->next_status_work, ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                }


            } else {
                $section->addText("II. Công việc dự kiến : Không có dữ liệu", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
            }

            if(!empty($item->Request)) {
                $section->addText("III. Kiến nghị:", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
                $section->addText($item->Request, ['size' => 11, 'bold' => false]);
                $section->addTextBreak(2);
            } else {
                $section->addText("III. Kiến nghị : " . 'Không có dữ liệu', ['size' => 11, 'bold' => true]);
                $section->addTextBreak(2);
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
        $record = $data['record'];
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        if(!empty($record)) {
            $dataDate =  $record->date_start;
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }

            $section->addText("              BÁO CÁO CÔNG VIỆC TUẦN ($startDateOfWeekInput – $endDateOfWeekInput)", ['size' => 12, 'bold' => true]);
            $section->addTextBreak(3);
        }
        // Lặp qua mảng dữ liệu và thêm từng dòng vào tài liệu Word
        $data = $data['mergedArray'];
        foreach ($data as $item) {
            // Thêm thông tin về phòng
            $section->addText($item['DepartmentName'], ['size' => 16, 'bold' => true]);
            $STTWorkDone = 1;
            $STTExpectedWork = 1;
            if(!empty($item['WorkDone'])) {


                // Thêm thông tin về công việc đã làm
                $section->addText("Công việc đã thực hiện:", ['size' => 14, 'bold' => true]);
                foreach ($item['WorkDone'] as $workDone) {
                    $sttWorkDone = $STTWorkDone++;
                    $section->addText("$sttWorkDone. Công việc đã thực hiện: ", ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    $section->addText("- Tiêu đề : " . $workDone['work_done'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                    if($workDone['description']) {
                        $section->addText("- Nội dung : " . $workDone['description'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone['start_date']) {
                        $section->addText("- Ngày bắt đầu : " . $workDone['start_date'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone['end_date']) {
                        $section->addText("- Ngày kết thúc : " . $workDone['end_date'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                    if($workDone['status_work']) {
                        $section->addText("- Tiến độ : " . $workDone['status_work'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                    }
                }

                // Thêm khoảng trắng giữa các phần
                $section->addTextBreak(1);
            } else {
                $section->addText("I. Công việc đã thực hiện : Không có dữ liệu", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
            }
            if(!empty($item['ExpectedWork'])) {

                 // Thêm thông tin về công việc dự kiến
                 $section->addText("II. Công việc dự kiến:", ['size' => 11, 'bold' => true]);
                 $section->addTextBreak(1);
                 foreach ($item['ExpectedWork'] as $expectedWork) {
                     $sttExpectedWork = $STTExpectedWork++;
                     $section->addText("$sttExpectedWork. Công việc dự kiến: ", ['size' => 11, 'bold' => false]);
                     $section->addTextBreak(1);
                     $section->addText("- Tiêu đề : " . $expectedWork['next_work'], ['size' => 11, 'bold' => false]);
                     $section->addTextBreak(1);
                     if($expectedWork['next_description']) {
                        $section->addText("- Nội dung : " . $expectedWork['next_description'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                     }
                     if($expectedWork['next_start_date']) {
                        $section->addText("- Ngày bắt đầu : " . $expectedWork['next_start_date'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                     }
                     if($expectedWork['next_end_date']) {
                        $section->addText("- Ngày kết thúc : " . $expectedWork['next_end_date'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                     }
                     if($expectedWork['next_status_work']) {
                        $section->addText("- Tiến độ : " . $expectedWork['next_status_work'], ['size' => 11, 'bold' => false]);
                        $section->addTextBreak(1);
                     }
                 }

            } else {
                $section->addText("II. Công việc dự kiến : Không có dữ liệu", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
            }

            if(!empty($item['Request'])) {
                $section->addText("III. Kiến nghị:", ['size' => 11, 'bold' => true]);
                $section->addTextBreak(1);
                $section->addText($item['Request'], ['size' => 11, 'bold' => false]);
                $section->addTextBreak(2);
            } else {
                $section->addText("III. Kiến nghị : " . 'Không có dữ liệu', ['size' => 11, 'bold' => true]);
                $section->addTextBreak(2);
            }

        }

        // Save the document as a Word file
        $filePath = storage_path('app/public/report-word.docx');

        $phpWord->save($filePath);

        // Return the generated Word file for download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public static function departmentWord($id) {
        // Truyền dữ liệu vào để in ra bản Word
        $data = Helper::reportWeekedDepartment($id);
        $record = $data['data'];
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        // Lặp qua mảng dữ liệu và thêm từng dòng vào tài liệu Word
        if(!empty($record)) {
            $dataDate =  $data['date'];

            $dateCarbon = Carbon::parse($dataDate);
            // $thisThursdayFormatted = Carbon::now()->endOfWeek()->subWeek()->addDays(5)->format('d-m-Y');

            $dayOfWeek = Carbon::parse($dateCarbon)->dayOfWeek;
            // dd($thisThursdayFormatted);

            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d/m/Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d/m/Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d/m/Y');
            }

            $section->addText("              BÁO CÁO CÔNG VIỆC TUẦN ($startDateOfWeekInput – $endDateOfWeekInput)", ['size' => 12, 'bold' => true]);
            $section->addTextBreak(3);
        }
        // $data = json_decode($data->values);
        // Thêm thông tin về phòng
        $section->addText($data['deparmentName'], ['size' => 11, 'bold' => true]);
        $section->addTextBreak(1);
        $STTWorkDone = 1;
        $STTExpectedWork = 1;
        $data = $data['data'];
        if(!empty($data['WorkDone'])) {
            // Thêm thông tin về công việc đã làm
            $section->addText("I. Công việc đã thực hiện:", ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
            foreach ($data['WorkDone'] as $workDone) {
                $sttWorkDone = $STTWorkDone++;
                $section->addText("$sttWorkDone. Công việc đã thực hiện: ", ['size' => 11, 'bold' => false]);
                $section->addTextBreak(1);
                $section->addText("- Tiêu đề : " . $workDone['work_done'], ['size' => 11, 'bold' => false]);
                $section->addTextBreak(1);
                if($workDone['description']) {
                    $section->addText("- Nội dung : " . $workDone['description'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($workDone['start_date']) {
                    $section->addText("- Ngày bắt đầu : " . $workDone['start_date'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($workDone['end_date']) {
                    $section->addText("- Ngày kết thúc : " . $workDone['end_date'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($workDone['status_work']) {
                    $section->addText("- Tiến độ : " . $workDone['status_work'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
            }

            // Thêm khoảng trắng giữa các phần
            $section->addTextBreak(1);
        } else {
            $section->addText("I. Công việc đã thực hiện : Không có dữ liệu", ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
        }
        if(!empty($data['ExpectedWork'])) {

            // Thêm thông tin về công việc dự kiến
            $section->addText("II. Công việc dự kiến:", ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
            foreach ($data['ExpectedWork'] as $expectedWork) {
                $sttExpectedWork = $STTExpectedWork++;
                $section->addText("$sttExpectedWork. Công việc dự kiến: ", ['size' => 11, 'bold' => false]);
                $section->addTextBreak(1);
                $section->addText("- Tiêu đề : " . $expectedWork['next_work'], ['size' => 11, 'bold' => false]);
                $section->addTextBreak(1);
                if($expectedWork['next_description']) {
                    $section->addText("- Nội dung : " . $expectedWork['next_description'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($expectedWork['next_start_date']) {
                    $section->addText("- Ngày bắt đầu : " . $expectedWork['next_start_date'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($expectedWork['next_end_date']) {
                    $section->addText("- Ngày kết thúc : " . $expectedWork['next_end_date'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
                if($expectedWork['next_status_work']) {
                    $section->addText("- Tiến độ : " . $expectedWork['next_status_work'], ['size' => 11, 'bold' => false]);
                    $section->addTextBreak(1);
                }
            }


        } else {
            $section->addText("II. Công việc dự kiến : Không có dữ liệu", ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
        }

        if(!empty($data['Request'])) {
            $section->addText("III. Kiến nghị:", ['size' => 11, 'bold' => true]);
            $section->addTextBreak(1);
            $section->addText($data['Request'], ['size' => 11, 'bold' => false]);
            $section->addTextBreak(2);
        } else {
            $section->addText("III. Kiến nghị : " . 'Không có dữ liệu', ['size' => 11, 'bold' => true]);
            $section->addTextBreak(2);
        }



        // Save the document as a Word file
        $filePath = storage_path('app/public/report-word-department.docx');

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

        $fileName = 'report.xlsx';
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

        $fileName = 'report.xlsx';
        $filePath = storage_path('app/public/') . $fileName;
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend();
    }

    public static function departmentExcel($id) {
        $data = Helper::reportWeekedDepartment($id);
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
        if (!empty($data['data'])) {
            $departmentName = $data['deparmentName'];
            $sheet->setCellValue('A' . $row, $departmentName);
            $startRow = $row;
            $requestRow = $row;
            $data = $data['data'];
            if (!empty($data['WorkDone'])) {
                foreach ($data['WorkDone'] as $workDone) {
                    $sheet->setCellValue('B' . $row, $workDone['work_done']);
                    $sheet->setCellValue('C' . $row, $workDone['start_date']);
                    $sheet->setCellValue('D' . $row, $workDone['end_date']);
                    $sheet->setCellValue('E' . $row, $workDone['status_work']);
                    $sheet->setCellValue('F' . $row, $workDone['description']);
                    $row++;
                }
            }
            if (!empty($data['ExpectedWork'])) {
                foreach ($data['ExpectedWork'] as $expectedWork) {
                    $sheet->setCellValue('G' . $startRow, $expectedWork['next_work']);
                    $sheet->setCellValue('H' . $startRow, $expectedWork['next_start_date']);
                    $sheet->setCellValue('I' . $startRow, $expectedWork['next_end_date']);
                    $sheet->setCellValue('J' . $startRow, $expectedWork['next_status_work']);
                    $sheet->setCellValue('K' . $startRow, $expectedWork['next_description']);
                    $startRow++;
                }
            }
            $request = $data['Request'];
            $sheet->setCellValue('L' . $requestRow, $request);
        }
        $writer = new Xlsx($spreadsheet);
        $fileName = 'report-department.xlsx';
        $filePath = storage_path('app/public/') . $fileName;
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend();
    }
}
