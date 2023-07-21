<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Dompdf\Dompdf;

class PdfController extends Controller
{
    public function exportPDF($id)
    {
        $data = Task::select('tasks.id', 'tasks.report_id', 'tasks.title', 'reports.id AS reports_id', 'tasks.status', 'tasks.created_at', 'tasks.updated_at')
            ->leftJoin('reports', 'tasks.report_id', '=', 'reports.id')
            ->where('tasks.report_id', $id)
            ->get();

        // Check if the report exists
        if (!$data) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        // Create a new Dompdf instance
        $dompdf = new Dompdf();
        $html = view('pdf.report', compact('data'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->stream('report_' . $id . '.pdf');
    }
}
