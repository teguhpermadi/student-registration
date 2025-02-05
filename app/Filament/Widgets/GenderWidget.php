<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GenderWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Laki-laki', Student::where('gender', 'male')->count()),
            Stat::make('Perempuan', Student::where('gender', 'female')->count()),
        ];
    }
}
