<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GenderWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Laki-laki', Student::notResign()->where('gender', 'male')->where('academic_year_id', AcademicYear::active()->first()->id)->count()),
            Stat::make('Perempuan', Student::notResign()->where('gender', 'female')->where('academic_year_id', AcademicYear::active()->first()->id)->count()),
        ];
    }
}
