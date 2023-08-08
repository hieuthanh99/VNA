<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;

class ExcelController extends Controller
{
    public function generateExcel() {
        return Helper::excel();
    }
}
