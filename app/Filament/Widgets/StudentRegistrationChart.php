<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class StudentRegistrationChart extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Pendaftaran Siswa';
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getRegistrationData();
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => array_values($data['counts']),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_keys($data['counts']),
        ];
    }

    protected function getFilters(): array
    {
        $filters = [
            'day' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
        ];

        // Add academic years to filters
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        foreach ($academicYears as $year) {
            $filters['year_' . $year->id] = 'Tahun ' . $year->year;
        }

        return $filters;
    }

    protected function getRegistrationData(): array
    {
        $filter = $this->filter ?? 'day';
        $now = now();
        $data = [];
        $labels = [];
        $counts = [];

        // Check if filter is for academic year
        if (str_starts_with($filter, 'year_')) {
            $academicYearId = (int) str_replace('year_', '', $filter);
            $academicYear = AcademicYear::find($academicYearId);
            
            if ($academicYear) {
                // Get all months in the academic year (assuming format 'YYYY/YYYY' like '2023/2024')
                $years = explode('/', $academicYear->year);
                $startYear = $years[0];
                
                for ($month = 1; $month <= 12; $month++) {
                    $monthName = Carbon::createFromDate($startYear, $month, 1)->locale('id')->monthName;
                    $labels[] = $monthName;
                    
                    $startDate = Carbon::createFromDate($startYear, $month, 1)->startOfMonth();
                    $endDate = Carbon::createFromDate($startYear, $month, 1)->endOfMonth();
                    
                    if ($month >= 7) { // If second semester (July-December), use first year
                        $startDate = Carbon::createFromDate($startYear, $month, 1)->startOfMonth();
                        $endDate = Carbon::createFromDate($startYear, $month, 1)->endOfMonth();
                    } else { // If first semester (January-June), use second year
                        $startDate = Carbon::createFromDate($startYear + 1, $month, 1)->startOfMonth();
                        $endDate = Carbon::createFromDate($startYear + 1, $month, 1)->endOfMonth();
                    }
                    
                    $count = Student::where('academic_year_id', $academicYearId)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                    
                    $counts[$monthName] = $count;
                }
            } else {
                // Fallback to current month if academic year not found
                $filter = 'month';
            }
        }
        
        if (!str_starts_with($filter, 'year_')) {
            switch ($filter) {
                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $dateRange = $this->generateDateRange($startDate, $endDate, 'day');
                    
                    foreach ($dateRange as $date) {
                        $formattedDate = $date->format('D, d M');
                        $labels[] = $formattedDate;
                        $counts[$formattedDate] = Student::whereDate('created_at', $date->format('Y-m-d'))->count();
                    }
                    break;

                case 'month':
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $dateRange = $this->generateDateRange($startDate, $endDate, 'day');
                    
                    foreach ($dateRange as $date) {
                        $formattedDate = $date->format('d M');
                        $labels[] = $formattedDate;
                        $counts[$formattedDate] = Student::whereDate('created_at', $date->format('Y-m-d'))->count();
                    }
                    break;

                default: // day
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    
                    for ($hour = 0; $hour < 24; $hour++) {
                        $hourStart = $startDate->copy()->addHours($hour);
                        $hourEnd = $startDate->copy()->addHours($hour + 1);
                        $hourLabel = $hourStart->format('H:00');
                        
                        $count = Student::whereBetween('created_at', [$hourStart, $hourEnd])->count();
                        $labels[] = $hourLabel;
                        $counts[$hourLabel] = $count;
                    }
                    break;
            }
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    protected function generateDateRange(Carbon $startDate, Carbon $endDate, string $interval = 'day'): array
    {
        $dates = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dates[] = $current->copy();
            $current->add(1, $interval);
        }

        return $dates;
    }
}
