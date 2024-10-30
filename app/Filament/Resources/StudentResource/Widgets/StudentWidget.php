<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Models\AcademicYear;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $academic = AcademicYear::find(1);
        $regular = Student::where('academic_year_id', 1)
            ->where('category', 'Regular')
            ->count();
        $inklusi = Student::where('academic_year_id', 1)
            ->where('category', 'Inklusi')
            ->count();

        if($academic){
            $quota_regular = $academic->quota_regular - $regular;
            $quota_inklusi = $academic->quota_inklusi - $inklusi;
        } else {
            $quota_regular = '0';
            $quota_inklusi = '0';
        }

        return [
            Stat::make('Jumlah Siswa Regular', $regular)
                ->description('Kuota tersisa ' . $quota_regular . ' siswa'),
            Stat::make('Jumlah Siswa Inklusi', $inklusi)
                ->description('Kuota tersisa ' . $quota_inklusi . ' siswa'),
        ];
    }
}
