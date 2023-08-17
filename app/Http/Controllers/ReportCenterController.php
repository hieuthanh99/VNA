<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCenter;
use App\Models\Logs;
use Carbon\Carbon;
use App\Models\Department;
use App\Helpers\Helper;

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
        //
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
        //
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
