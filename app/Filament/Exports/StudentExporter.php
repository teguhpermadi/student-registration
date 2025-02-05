<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user_id'),
            ExportColumn::make('academic_year_id'),
            ExportColumn::make('category'),
            ExportColumn::make('full_name'),
            ExportColumn::make('nick_name'),
            ExportColumn::make('gender'),
            ExportColumn::make('city_born'),
            ExportColumn::make('birthday'),
            ExportColumn::make('hobby'),
            ExportColumn::make('nisn'),
            ExportColumn::make('nik'),
            ExportColumn::make('number_akta_lahir'),
            ExportColumn::make('number_kartu_keluarga'),
            ExportColumn::make('address'),
            ExportColumn::make('village'),
            ExportColumn::make('district'),
            ExportColumn::make('city'),
            ExportColumn::make('province'),
            ExportColumn::make('previous_school'),
            ExportColumn::make('address_previous_school'),
            ExportColumn::make('poscode'),
            ExportColumn::make('father_nik'),
            ExportColumn::make('father_name'),
            ExportColumn::make('father_city_born'),
            ExportColumn::make('father_birthday'),
            ExportColumn::make('father_religion'),
            ExportColumn::make('father_education'),
            ExportColumn::make('father_relation'),
            ExportColumn::make('father_job'),
            ExportColumn::make('father_income'),
            ExportColumn::make('father_phone'),
            ExportColumn::make('mother_nik'),
            ExportColumn::make('mother_name'),
            ExportColumn::make('mother_city_born'),
            ExportColumn::make('mother_birthday'),
            ExportColumn::make('mother_religion'),
            ExportColumn::make('mother_education'),
            ExportColumn::make('mother_relation'),
            ExportColumn::make('mother_job'),
            ExportColumn::make('mother_income'),
            ExportColumn::make('mother_phone'),
            ExportColumn::make('date_received'),
            ExportColumn::make('grade_received'),
            ExportColumn::make('scan_akta_lahir'),
            ExportColumn::make('scan_kartu_keluarga'),
            ExportColumn::make('scan_ktp_ayah'),
            ExportColumn::make('scan_ktp_ibu'),
            ExportColumn::make('scan_nisn'),
            ExportColumn::make('photo'),
            ExportColumn::make('ttd_name'),
            ExportColumn::make('ttd'),
            ExportColumn::make('agreement'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
