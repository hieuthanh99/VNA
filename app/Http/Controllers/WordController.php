<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Helpers\Helper;

class WordController extends Controller
{
    public function generateWord()
    {
        return Helper::word();
    }

    public function generateWordDetails($id)
    {
        return Helper::generateWordDetail($id);
    }

    public function departmentWord($id)
    {
        return Helper::departmentWord($id);
    }
}
