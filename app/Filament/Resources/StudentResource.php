<?php

namespace App\Filament\Resources;

use App\CategoryEnum;
use App\ChildRelationEnum;
use App\EducationEnum;
use App\IncomeEnum;
use App\JobEnum;
use App\Models\Student;
use App\Models\User;
use App\ReligionEnum;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Section::make('Formulir PPDB')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->options(AcademicYear::all()->pluck('year', 'id'))
                            ->required(),
                        Select::make('category')
                            ->options(CategoryEnum::class)
                            ->live()
                            ->required(),
                    ]),
                Section::make('Student Identity')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('nick_name')
                            ->translateLabel()
                            ->required(),
                        Select::make('gender')
                            ->translateLabel()
                            ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                            ->required(),
                        TextInput::make('city_born')
                            ->translateLabel()
                            ->required(),
                        DatePicker::make('birthday')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('nisn')
                            ->translateLabel()
                            ->helperText('nomor induk siswa nasional')
                            ->required(),
                        TextInput::make('nik')
                            ->helperText('nomor induk kependudukan siswa')
                            ->translateLabel()
                            ->required(),
                        FileUpload::make('photo')
                            ->openable()
                            ->image()
                            ->helperText('Pas foto 3x4')
                            ->required(),
                    ]),
                Section::make('Address')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('village')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('district')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('city')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('province')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('poscode')
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('Data Previous School')
                    ->columns(2)
                    ->schema([
                        TextInput::make('previous_school')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('address_previous_school')
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('Father Identity')
                    ->columns(2)
                    ->schema([
                        Select::make('father_status')
                            ->options([
                                'alive' => 'Hidup',
                                'die' => 'Meninggal dunia'
                            ])
                            ->default('alive')
                            ->required(),
                        TextInput::make('father_nik')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('father_name')
                            ->translateLabel()
                            ->required(),
                        DatePicker::make('father_birthday')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('father_city_born')
                            ->translateLabel()
                            ->required(),
                        Select::make('father_religion')
                            ->translateLabel()
                            ->options(ReligionEnum::class)
                            ->required(),
                        Select::make('father_education')
                            ->translateLabel()
                            ->options(EducationEnum::class)
                            ->required(),
                        Radio::make('father_relation')
                            ->translateLabel()
                            ->options(ChildRelationEnum::class)
                            ->required(),
                        Radio::make('father_job')
                            ->translateLabel()
                            ->options(JobEnum::class)
                            ->required(),
                        Radio::make('father_income')
                            ->translateLabel()
                            ->options(IncomeEnum::class)
                            ->required(),
                        TextInput::make('father_phone')
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('Mother Identity')
                    ->columns(2)
                    ->schema([
                        Select::make('mother_status')
                            ->options([
                                'alive' => 'Hidup',
                                'die' => 'Meninggal dunia'
                            ])
                            ->default('alive')
                            ->required(),
                        TextInput::make('mother_nik')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('mother_name')
                            ->translateLabel()
                            ->required(),
                        DatePicker::make('mother_birthday')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('mother_city_born')
                            ->translateLabel()
                            ->required(),
                        Select::make('mother_religion')
                            ->translateLabel()
                            ->options(ReligionEnum::class)
                            ->required(),
                        Select::make('mother_education')
                            ->translateLabel()
                            ->options(EducationEnum::class)
                            ->required(),
                        Radio::make('mother_relation')
                            ->translateLabel()
                            ->options(ChildRelationEnum::class)
                            ->required(),
                        Radio::make('mother_job')
                            ->translateLabel()
                            ->options(JobEnum::class)
                            ->required(),
                        Radio::make('mother_income')
                            ->translateLabel()
                            ->options(IncomeEnum::class)
                            ->required(),
                        TextInput::make('mother_phone')
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('File Upload')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('scan_akta_lahir')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_kartu_keluarga')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_ktp_ayah')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_ktp_ibu')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_nisn')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),

                    ]),
                Section::make('Agreement')
                    ->schema([
                        Checkbox::make('agreement')
                            ->label('Data yang sudah saya isikan adalah benar.')
                            ->accepted(),
                        TextInput::make('ttd_name')
                            ->required(),
                        Grid::make([
                            'default' => 2,
                        ])
                            ->schema([
                                // SignaturePad::make('signature')
                                //     ->label(__('Sign here'))
                                //     ->downloadable(false)
                                //     ->backgroundColor('white')
                                //     ->undoable()
                                //     ->live()
                                //     ->visible(fn($get) => empty($get('ttd')))
                                //     ->confirmable(true)
                                //     ->afterStateUpdated(function ($state, $livewire) {
                                //         if ($state) {
                                //             $base64_string = substr($state, strpos($state, ',') + 1);
                                //             $image_data = base64_decode($base64_string);
                                //             $file_name = Str::random(40);
                                //             $file = self::createTemporaryFileUploadFromUrl($image_data, $file_name);
                                //             $livewire->dispatch('test', $file);
                                //         }
                                //     }),
                                FileUpload::make('ttd')
                                    ->label('Signature Result')
                                    ->downloadable()
                                    ->disabled()
                                    ->dehydrated()
                                    ->directory('signature')
                                    ->required()
                                    // ->hintAction(
                                    //     Action::make('Delete')
                                    //         ->icon('heroicon-m-trash')
                                    //         ->visible(fn($state) => filled($this->user['ttd']) || $state)
                                    //         ->visible(fn($state) => filled($this->user) || $state)
                                    //         ->requiresConfirmation()
                                    //         ->action(function ($state, $set) {
                                    //             if (!empty($this->user['ttd'] ?? null)) {
                                    //                 Storage::disk('public')->delete($this->user['ttd']);
                                    //                 $this->user['ttd'] = null;
                                    //                 $this->user->save();

                                    //                 return redirect(request()->header('Referer'));
                                    //             } else {
                                    //                 $file = reset($state);
                                    //                 if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    //                     Storage::delete($file->getPathName());
                                    //                     $set('ttd', null);
                                    //                 }
                                    //             }
                                    //         })
                                    // )
                                    ->extraAlpineAttributes([
                                        'x-on:test.window' => '
                                                const pond = FilePond.find($el.querySelector(".filepond--root"));
                                                setTimeout(() => {
                                                    pond.removeFiles({ revert: false });
                                                    pond.addFile($event.detail);
                                                }, 750);',
                                    ])
                                    ->image(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->circular(),
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('gender'),
                TextColumn::make('category'),
                TextColumn::make('previous_school'),
                TextColumn::make('updated_at'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Regular' => 'Regular',
                        'Inklusi' => 'Inklusi',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                ActionsAction::make('download')
                    ->url(fn(Student $record): string => route('print-preview', $record))
                    ->openUrlInNewTab(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
