<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function print_preview($id)
    {
        $student = Student::find($id);
        $pdf = Pdf::loadView('print-preview', ['student' => $student]);
        // return $pdf->download();
        // dd($student);
        return view('print-preview', ['student' => $student]);
    }
}
