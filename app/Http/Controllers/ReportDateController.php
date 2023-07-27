<?php

namespace App\Http\Controllers;

use App\Models\ReportDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // Tính toán ngày báo cáo kết thúc sau 7 ngày
        // $endOfWeek = Carbon::createFromFormat('Y-m-d', $reportDate)->addDays(7)->toDateString();
        ReportDate::create([
            'report_date' => $reportDate,
        ]);

        return redirect()->route('report-dates.index', compact('reportDate'))->with('success', 'Ngày báo cáo đã được đặt thành công.');
    }
}