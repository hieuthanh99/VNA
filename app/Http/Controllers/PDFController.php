<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Helpers\Helper;

class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF()
    {
        return Helper::pdf();
    }
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDFDetails($id)
    {
        return Helper::pdfDetails($id);
    }
}
