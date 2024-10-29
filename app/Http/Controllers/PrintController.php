<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function print_preview($id)
    {
        $student = Student::find($id);
        // dd($student);
        return view('print-preview', ['student' => $student]);
    }
}
