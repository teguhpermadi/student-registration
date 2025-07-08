<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            // create action to url student-registration
            Actions\CreateAction::make()
                ->url(fn () => url('admin/student-registration'))
                ->label('Register New Student')
                ->icon('heroicon-o-plus')
                ->color('primary')
        ];
    }
}
