<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class PrintController extends Controller
{
    public function download($id)
    {
        $student = Student::find($id);
        $templateProcessor = new TemplateProcessor(storage_path('/app/public/template/template formulir.docx'));

        $templateProcessor->setValue('year', $student->academicYear->year);
        $templateProcessor->setValue('category', $student->category);
        $templateProcessor->setValue('full_name', $student->full_name);
        $templateProcessor->setValue('nick_name', $student->nick_name);
        $templateProcessor->setValue('gender', $student->gender);
        $templateProcessor->setValue('city_born', $student->city_born);
        $templateProcessor->setValue('birthday', $student->birthday);
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('nik', $student->nik);
        $templateProcessor->setValue('number_akta_lahir', $student->number_akta_lahir);
        $templateProcessor->setValue('number_kartu_keluarga', $student->number_kartu_keluarga);
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
        $filename = $student->full_name . '.docx';
        $file_path = storage_path('/app/public/' . $filename);
        $templateProcessor->saveAs($file_path);

        // return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
        return $this->zipArchive($id);
    }

    public function zipArchive($id)
    {
        $student = Student::find($id);
        $filename = $student->full_name . '.docx';
        $file_path = storage_path('app/public/' . $filename);

        $zip_file = $student->full_name . '.zip'; // Name of our archive to download

        // Initializing PHP class
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Adding file: second parameter is what will the path inside of the archive
        // So it will create another folder called "storage/" inside ZIP, and put the file there.
        $zip->addFile(storage_path('/app/public/'.$filename), $filename);
        $zip->addFile(storage_path('/app/public/'.$student->photo), $student->photo);
        $zip->addFile(storage_path('/app/public/'.$student->scan_akta_lahir), $student->scan_akta_lahir);
        $zip->addFile(storage_path('/app/public/'.$student->scan_kartu_keluarga), $student->scan_akta_lahir);
        $zip->addFile(storage_path('/app/public/'.$student->scan_kartu_keluarga), $student->scan_kartu_keluarga);
        $zip->addFile(storage_path('/app/public/'.$student->scan_ktp_ayah), $student->scan_ktp_ayah);
        $zip->addFile(storage_path('/app/public/'.$student->scan_ktp_ibu), $student->scan_ktp_ibu);
        $zip->addFile(storage_path('/app/public/'.$student->scan_nisn), $student->scan_nisn);

        $zip->close();

        // delete file word
        unlink($file_path);

        // We return the file immediately after download
        return response()->download($zip_file)->deleteFileAfterSend(true);
    }

    public function letter($id)
    {
        $student = Student::find($id);

        // buat suratnya
        $templateProcessor = new TemplateProcessor(storage_path('app/public/template/template surat penerimaan.docx'));
            
        $reference_number = 'S.KET/'. $student->id. '/PPDB/MIAR/' . $student->updated_at->year;
        
        $templateProcessor->setValue('reference_number', $reference_number);
        $templateProcessor->setValue('year', $student->academicYear->year);
        $templateProcessor->setValue('full_name', Str::upper($student->full_name));
        $templateProcessor->setValue('city_born', Str::upper($student->city_born));
        $templateProcessor->setValue('birthday', Carbon::parse($student->birthday)->format('j F Y'));
        $templateProcessor->setValue('previous_school', Str::upper($student->previous_school));
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('category', $student->category);
        $templateProcessor->setValue('date', $student->updated_at->format('j F Y'));

        // save docx
        $filename = 'suket ppdb '.$student->full_name . '.docx';
        $file_path = storage_path('app/public/letter/' . $filename);
        $templateProcessor->saveAs($file_path);

        // save to database
        $letter = Letter::updateOrCreate([
            'reference_number' => $reference_number,
        ],[
            'reference_number' => $reference_number,
            'student_id' => $id,
            'academic_year_id' => $student->academicYear->id,
            'number_reference' => $reference_number,
            'file' => $filename,
        ]);

        return response()->download($file_path); // <<< HERE

    }
}
