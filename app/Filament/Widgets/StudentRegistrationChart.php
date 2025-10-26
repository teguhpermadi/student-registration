<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Debug: Log the start of the function
        Log::info('getRegistrationData called with filter: ' . ($this->filter ?? 'day'));
        
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
                
                // Get the academic year start and end dates
                $academicStart = Carbon::parse($academicYear->start_date);
                $academicEnd = Carbon::parse($academicYear->end_date);
                
                // Generate monthly intervals between academic start and end dates
                $current = $academicStart->copy()->startOfMonth();
                
                while ($current <= $academicEnd) {
                    $monthName = $current->locale('id')->monthName . ' ' . $current->year;
                    $labels[] = $monthName;
                    
                    $startDate = $current->copy()->startOfMonth();
                    $endDate = $current->copy()->endOfMonth();
                    
                    // Adjust end date if it's beyond the academic year end
                    if ($endDate > $academicEnd) {
                        $endDate = $academicEnd->copy()->endOfDay();
                    }
                    
                    // Debug: Log the academic year and month being processed
                    Log::info("Processing academic year ID: $academicYearId, Month: $monthName ($startYear)");
                    
                    // Set the date range for the query with proper timezone handling
                    $startOfDay = Carbon::parse($startDate)->startOfDay()->setTimezone('UTC');
                    $endOfDay = Carbon::parse($endDate)->endOfDay()->setTimezone('UTC');
                    
                    // Debug: Log the date range being queried
                    Log::info("Date range: {$startDate->toDateString()} to {$endDate->toDateString()} (UTC: {$startOfDay->toDateTimeString()} to {$endOfDay->toDateTimeString()})");
                    
                    // Get the raw SQL query for debugging
                    $query = Student::query()
                        ->where('academic_year_id', $academicYearId)
                        ->where('is_resign', false)
                        ->whereDate('created_at', '>=', $startDate->toDateString())
                        ->whereDate('created_at', '<=', $endDate->toDateString());
                        
                    // Debug: Log the raw SQL query
                    Log::info('SQL Query: ' . $query->toSql());
                    Log::info('Bindings: ' . json_encode($query->getBindings()));
                    
                    $count = $query->count();
                    
                    // Debug: Log the count result
                    Log::info("Found $count students for $monthName");
                    
                    $counts[$monthName] = $count;
                    
                    // Move to next month
                    $current->addMonth();
                    
                    // Debug: Log the current month being processed
                    Log::info("Processed month: $monthName ($startDate to $endDate) - Found $count students");
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
                        $startOfDay = $date->copy()->startOfDay()->timezone(config('app.timezone'));
                        $endOfDay = $date->copy()->endOfDay()->timezone(config('app.timezone'));
                        
                        $counts[$formattedDate] = Student::query()
                            ->where('is_resign', false)
                            ->whereDate('created_at', $date->toDateString())
                            ->count();
                    }
                    break;

                case 'month':
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $dateRange = $this->generateDateRange($startDate, $endDate, 'day');
                    
                    foreach ($dateRange as $date) {
                        $formattedDate = $date->format('d M');
                        $labels[] = $formattedDate;
                        $startOfDay = $date->copy()->startOfDay()->timezone(config('app.timezone'));
                        $endOfDay = $date->copy()->endOfDay()->timezone(config('app.timezone'));
                        
                        $counts[$formattedDate] = Student::query()
                            ->where('is_resign', false)
                            ->whereDate('created_at', $date->toDateString())
                            ->count();
                    }
                    break;

                default: // day
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    
                    for ($hour = 0; $hour < 24; $hour++) {
                        $hourStart = $startDate->copy()->addHours($hour);
                        $hourEnd = $startDate->copy()->addHours($hour + 1);
                        $hourLabel = $hourStart->format('H:00');
                        
                        $hourStartTz = $hourStart->copy()->timezone(config('app.timezone'));
                        $hourEndTz = $hourEnd->copy()->timezone(config('app.timezone'));
                        
                        $count = Student::query()
                            ->where('is_resign', false)
                            ->where('created_at', '>=', $hourStartTz->toDateTimeString())
                            ->where('created_at', '<', $hourEndTz->toDateTimeString())
                            ->count();
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
