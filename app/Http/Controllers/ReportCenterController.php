<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCenter;
use App\Models\Logs;
use Carbon\Carbon;
use App\Models\Department;
use App\Helpers\Helper;
use App\Models\Task;
use App\Models\Report;

class ReportCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = Helper::reportWeeked();
        $mergedArray = $result['mergedArray'] ?? null;
        $startDate = $result['startDate'] ?? null;
        $endDateWeek = $result['endDateWeek'] ?? null;

        $lastFriday = $startDate->copy()->subDays($startDate->dayOfWeek + 2);
        $thisThursday = $startDate->copy()->addDays(3 - $startDate->dayOfWeek + 1);

        $lastFridayFormatted = $lastFriday->format('d-m-Y');
        $thisThursdayFormatted = $thisThursday->format('d-m-Y');
        $record = $result['record'] ?? null;
        if(!empty($record)) {
            $data =  $record->date_start;
            $dateCarbon = Carbon::parse($data);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if(!empty($record)) {
                if ($dayOfWeek > 5) {
                    $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d-m-Y');
                    $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d-m-Y');
                } else {
                    $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d-m-Y');
                    $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d-m-Y');
                }
            }
            return view('centers.index', ['record' => $record,'startDateOfWeekInput' => $startDateOfWeekInput,'endDateOfWeekInput' => $endDateOfWeekInput , 'data' => $mergedArray, 'startDate' => $startDate->format('d-m-Y'), 'endDate' => $endDateWeek->format('d-m-Y')]);
        }
        return view('centers.index', ['record' => $record, 'data' => $mergedArray, 'startDate' => $lastFridayFormatted, 'endDate' => $thisThursdayFormatted]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $dataCenter = ReportCenter::find($id);
        $result = Helper::reportWeeked();
        $record = $result['record'] ?? null;
        $startDate = $result['startDate'] ?? null;
        $endDateWeek = $result['endDateWeek'] ?? null;
        if(!empty($record)) {
            $data =  $record->date_start;
            $dateCarbon = Carbon::parse($data);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
           
            if(!empty($record)) {
                if ($dayOfWeek > 5) {
                    $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d-m-Y');
                    $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d-m-Y');
                } else {
                    $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d-m-Y');
                    $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d-m-Y');
                }
            }
            return view('centers.edit', ['dataCenter' => $dataCenter, 'record' => $record,'startDateOfWeekInput' => $startDateOfWeekInput,'endDateOfWeekInput' => $endDateOfWeekInput, 'startDate'=> $startDate, 'endDate'=> $endDateWeek]);
        }
        return view('centers.edit', ['dataCenter' => $dataCenter, 'startDate'=> $startDate, 'endDate'=> $endDateWeek]);
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
        $reportCenter = ReportCenter::find($id);
        $jsonArray = json_decode($reportCenter->values);

        foreach ($request->all() as $key => $item) {
            //WorkDone
            if (strpos($key, 'cong_viec_da_lam') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->WorkDone[$parts[3]]->work_done = $item;
            }
            if (strpos($key, 'start_date_tuan_nay') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->WorkDone[$parts[3]]->start_date = $item;
            }
            if (strpos($key, 'end_date_tuan_nay') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->WorkDone[$parts[3]]->end_date = $item;
            }
            if (strpos($key, 'trangthai_cong_viec_tuan_nay') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->WorkDone[$parts[3]]->status_work = $item;
            }
            if (strpos($key, 'noi_dung_cong_viec_tuan_nay') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->WorkDone[$parts[3]]->description = $item;
            }
            //ExpectedWork
            if (strpos($key, 'tieu_de_cong_viec_tuan_toi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->ExpectedWork[$parts[3]]->next_work = $item;
            }
            if (strpos($key, 'start_date_tuan_toi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->ExpectedWork[$parts[3]]->next_start_date = $item;
            }
            if (strpos($key, 'end_date_tuan_toi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->ExpectedWork[$parts[3]]->next_end_date = $item;
            }
            if (strpos($key, 'trangthai_congviec_tuan_toi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->ExpectedWork[$parts[3]]->next_status_work = $item;
            }
            if (strpos($key, 'noi_dung_cong_viec_tuan_toi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->ExpectedWork[$parts[3]]->next_description = $item;
            }



            if (strpos($key, 'kien_nghi') !== false) {
                $parts = explode("*_*", $key);
                $jsonArray[$parts[1]]->Request = $item;
            }
        }

        $jsonData = json_encode($jsonArray);
        $reportCenter->values = $jsonData;
        $reportCenter->save();
        
        return redirect()->route('centers.index')->with(['success' => 'Dữ liệu đã được lưu thành công.']);
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
