<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintController extends Controller
{
    public function print_preview($id)
    {
        $student = Student::find($id);
        //$pdf = Pdf::loadView('print-preview', ['student' => $student]);
        // return $pdf->download();
        // dd($student);
        //return view('print-preview', ['student' => $student]);
        $templateProcessor = new TemplateProcessor(storage_path('/template/template formulir.docx'));
        $templateProcessor->setValue('year', $student->academicYear->year);
        $templateProcessor->setValue('category', $student->category);
        $templateProcessor->setValue('full_name', $student->full_name);
        $templateProcessor->setValue('nick_name', $student->nick_name);
        $templateProcessor->setValue('gender', $student->gender);
        $templateProcessor->setValue('city_born', $student->city_born);
        $templateProcessor->setValue('birthday', $student->birthday);
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('nik', $student->nik);
        $templateProcessor->setValue('address', $student->address);
        $templateProcessor->setValue('village', $student->village);
        $templateProcessor->setValue('district', $student->district);
        $templateProcessor->setValue('city', $student->city);
        $templateProcessor->setValue('province', $student->province);
        $templateProcessor->setValue('previous_school', $student->previous_school);
        $templateProcessor->setValue('address_previous_school', $student->address_previous_school);
        $templateProcessor->setValue('poscode', $student->poscode);
        $templateProcessor->setValue('father_nik', $student->father_nik);
        $templateProcessor->setValue('father_name', $student->father_name);
        $templateProcessor->setValue('father_city_born', $student->father_city_born);
        $templateProcessor->setValue('father_birthday', $student->father_birthday);
        $templateProcessor->setValue('father_religion', $student->father_religion);
        $templateProcessor->setValue('father_education', $student->father_education);
        $templateProcessor->setValue('father_relation', $student->father_relation);
        $templateProcessor->setValue('father_job', $student->father_job);
        $templateProcessor->setValue('father_income', $student->father_income);
        $templateProcessor->setValue('father_phone', $student->father_phone);
        $templateProcessor->setValue('mother_nik', $student->mother_nik);
        $templateProcessor->setValue('mother_name', $student->mother_name);
        $templateProcessor->setValue('mother_city_born', $student->mother_city_born);
        $templateProcessor->setValue('mother_birthday', $student->mother_birthday);
        $templateProcessor->setValue('mother_religion', $student->mother_religion);
        $templateProcessor->setValue('mother_education', $student->mother_education);
        $templateProcessor->setValue('mother_relation', $student->mother_relation);
        $templateProcessor->setValue('mother_job', $student->mother_job);
        $templateProcessor->setValue('mother_income', $student->mother_income);
        $templateProcessor->setValue('mother_phone', $student->mother_phone);
        $templateProcessor->setValue('updated_at', $student->updated_at->format('j F Y'));
        
        $templateProcessor->setImageValue('photo', storage_path('/app/public/' . $student->photo));
        $templateProcessor->setImageValue('ttd', storage_path('/app/public/' . $student->ttd));
        $templateProcessor->setValue('ttd_name', $student->ttd_name);

        // save docx
        $filename = 'formulir-ppdb-' . $student->full_name . '.docx';
        $file_path = storage_path('/app/public/' . $filename);
        $templateProcessor->saveAs($file_path);
        
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }
}
