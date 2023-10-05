<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use PhpOffice\PhpWord\Writer\HTML\Part\Head;

class ExcelController extends Controller
{
    public function generateExcel() {
        return Helper::excel();
    }

    public function generateExcelDetails($id) {
        return Helper::excelDetail($id);
    }

    public function departmentExcel($id) {
        return Helper::departmentExcel($id);
    }
}
