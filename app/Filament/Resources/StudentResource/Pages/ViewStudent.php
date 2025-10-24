<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Student;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('resign')
                ->label('Resign')
                ->action(function (Student $record) {
                    $record->update([
                        'is_resign' => true,
                    ]);

                    // redirect to index page
                    return redirect()->route('filament.admin.resources.students.index');
                })
                ->visible(function (Student $record) {
                    return $record->is_resign == false;
                })
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Resign Student')
                ->modalDescription('Are you sure you want to resign this student?'),

            Actions\Action::make('unresign')
                ->label('Unresign')
                ->action(function (Student $record) {
                    $record->update([
                        'is_resign' => false,
                    ]);

                    // redirect to index page
                    return redirect()->route('filament.admin.resources.students.index');
                })
                ->visible(function (Student $record) {
                    return $record->is_resign == true;
                })
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Unresign Student')
                ->modalDescription('Are you sure you want to unresign this student?'),
        ];
    }
}
