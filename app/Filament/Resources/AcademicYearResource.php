<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('academic_year');
    }

    protected static ?string $title = 'Tahun Akademik';

    protected static ?string $breadcrumb = 'Tahun Akademik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('year')
                    ->mask('9999/9999')
                    ->label(__('year'))
                    ->required(),
                TextInput::make('quota_regular')
                    ->label(__('quota_regular'))
                    ->numeric()
                    ->required(),
                TextInput::make('quota_inklusi')
                    ->label(__('quota_inklusi'))
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label(__('year')),
                TextColumn::make('quota_regular')
                    ->label(__('quota_regular')),
                TextColumn::make('quota_inklusi')
                    ->label(__('quota_inklusi')),
                TextColumn::make('student_count')
                    ->label(__('student_count'))
                    ->counts('student'),
                ToggleColumn::make('active')
                    ->label(__('active')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAcademicYears::route('/'),
        ];
    }
}
