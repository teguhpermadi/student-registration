<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // tabs
    public function getTabs(): array
    {
        if (auth()->user()->hasRole('admin')) {
            return [
                'not_resign' => Tab::make('Aktif')
                    ->query(fn (Builder $query) => $query->where('is_resign', false)),
                'resign' => Tab::make('Mengundurkan Diri')
                    ->query(fn (Builder $query) => $query->where('is_resign', true)),
            ];
        }
        
        return [];
    }
}
