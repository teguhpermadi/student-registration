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
use Filament\Forms\Get;
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

    public static function getNavigationLabel(): string
    {
        return __('student');
    }

    protected static ?string $title = 'Siswa';

    protected static ?string $breadcrumb = 'Siswa';

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
                            ->label(__('academic_year_id'))
                            ->reactive()
                            ->required(),
                        Select::make('category')
                            ->options([
                                'Regular' => 'Regular',
                                'Inklusi' => 'Inklusi',
                            ])
                            ->label(__('category'))
                            ->reactive()
                            ->required(),
                    ]),
                Section::make('Identitas Siswa')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label(__('full_name'))
                            ->dehydrateStateUsing(fn(string $state): string => ucwords($state))
                            ->required(),
                        TextInput::make('nick_name')
                            ->label(__('nick_name'))
                            ->required(),
                        Select::make('gender')
                            ->label(__('gender'))
                            ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                            ->required(),
                        TextInput::make('city_born')
                            ->label(__('city_born'))
                            ->required(),
                        DatePicker::make('birthday')
                            ->label(__('birthday'))
                            ->required(),
                        TextInput::make('nisn')
                            ->label(__('nisn'))
                            ->numeric()
                            ->helperText('nomor induk siswa nasional')
                            ->required(),
                        TextInput::make('nik')
                            ->numeric()
                            ->helperText('nomor induk kependudukan siswa')
                            ->label(__('nik'))
                            ->required(),
                        TextInput::make('number_akta_lahir')
                            ->helperText('nomor akta lahir')
                            ->label(__('number_akta_lahir'))
                            ->required(),
                        TextInput::make('number_kartu_keluarga')
                            ->helperText('nomor kartu keluarga')
                            ->label(__('number_kartu_keluarga'))
                            ->required(),
                        FileUpload::make('photo')
                            ->openable()
                            ->directory('photo')
                            ->image()
                            ->label(__('photo'))
                            ->helperText('Pas foto 3x4')
                            ->required(),
                    ]),
                Section::make('Alamat')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label(__('address'))
                            ->required(),
                        TextInput::make('village')
                            ->label(__('village'))
                            ->required(),
                        TextInput::make('district')
                            ->label(__('district'))
                            ->required(),
                        TextInput::make('city')
                            ->label(__('city'))
                            ->required(),
                        TextInput::make('province')
                            ->label(__('province'))
                            ->required(),
                        TextInput::make('poscode')
                            ->numeric()
                            ->label(__('poscode'))
                            ->required(),
                    ]),
                Section::make('Data Sekolah Sebelumnya')
                    ->columns(2)
                    ->schema([
                        TextInput::make('previous_school')
                            ->label(__('previous_school'))
                            ->required(),
                        TextInput::make('address_previous_school')
                            ->label(__('address_previous_school'))
                            ->required(),
                    ]),
                Section::make('Identitas Ayah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('father_nik')
                            ->label(__('father_nik'))
                            ->reactive()
                            ->required(),
                        TextInput::make('father_name')
                            ->label(__('father_name'))
                            ->required(),
                        DatePicker::make('father_birthday')
                            ->label(__('father_birthday'))
                            ->required(),
                        TextInput::make('father_city_born')
                            ->label(__('father_city_born'))
                            ->required(),
                        Select::make('father_religion')
                            ->label(__('father_religion'))
                            ->options(ReligionEnum::class)
                            ->required(),
                        Select::make('father_education')
                            ->label(__('father_education'))
                            ->options(EducationEnum::class)
                            ->required(),
                        Radio::make('father_relation')
                            ->label(__('father_relation'))
                            ->options(ChildRelationEnum::class)
                            ->required(),
                        Radio::make('father_job')
                            ->label(__('father_job'))
                            ->options(JobEnum::class)
                            ->required(),
                        Radio::make('father_income')
                            ->label(__('father_income'))
                            ->options(IncomeEnum::class)
                            ->required(),
                        TextInput::make('father_phone')
                            ->prefix('+62')
                            ->numeric()
                            ->label(__('father_phone'))
                            ->required(),
                    ]),
                Section::make('Identitas Ibu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('mother_nik')
                            ->label(__('mother_nik'))
                            ->required(),
                        TextInput::make('mother_name')
                            ->label(__('mother_name'))
                            ->required(),
                        DatePicker::make('mother_birthday')
                            ->label(__('mother_birthday'))
                            ->required(),
                        TextInput::make('mother_city_born')
                            ->label(__('mother_city_born'))
                            ->required(),
                        Select::make('mother_religion')
                            ->label(__('mother_religion'))
                            ->options(ReligionEnum::class)
                            ->required(),
                        Select::make('mother_education')
                            ->label(__('mother_education'))
                            ->options(EducationEnum::class)
                            ->required(),
                        Radio::make('mother_relation')
                            ->label(__('mother_relation'))
                            ->options(ChildRelationEnum::class)
                            ->required(),
                        Radio::make('mother_job')
                            ->label(__('mother_job'))
                            ->options(JobEnum::class)
                            ->required(),
                        Radio::make('mother_income')
                            ->label(__('mother_income'))
                            ->options(IncomeEnum::class)
                            ->required(),
                        TextInput::make('mother_phone')
                            ->prefix('+62')
                            ->numeric()
                            ->label(__('mother_phone'))
                            ->required(),
                    ]),
                Section::make('Unggah File')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('scan_akta_lahir')
                            ->openable()
                            ->directory('akta_lahir')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_kartu_keluarga')
                            ->openable()
                            ->directory('kartu_keluarga')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_ktp_ayah')
                            ->openable()
                            ->directory('ktp')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_ktp_ibu')
                            ->openable()
                            ->directory('ktp')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        FileUpload::make('scan_nisn')
                            ->openable()
                            ->directory('nisn')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),

                    ]),
                Section::make('Persetujuan')
                    ->schema([
                        Checkbox::make('agreement')
                            ->label('Data yang sudah saya isikan adalah benar.')
                            ->accepted(),
                        TextInput::make('ttd_name')
                            ->label(__('ttd_name'))
                            ->required(),
                        Grid::make([
                            'default' => 2,
                        ])
                            ->schema([
                                SignaturePad::make('signature')
                                    ->label(__('ttd'))
                                    ->downloadable(false)
                                    ->backgroundColor('white')
                                    ->undoable()
                                    ->live()
                                    ->visible(fn($get) => empty($get('ttd')))
                                    ->confirmable(true)
                                    ->afterStateUpdated(function ($state, $livewire) {
                                        if ($state) {
                                            $base64_string = substr($state, strpos($state, ',') + 1);
                                            $image_data = base64_decode($base64_string);
                                            $file_name = Str::random(40);
                                            $file = self::createTemporaryFileUploadFromUrl($image_data, $file_name);
                                            $livewire->dispatch('test', $file);
                                        }
                                    }),
                                FileUpload::make('ttd')
                                    ->label(__('ttd'))
                                    ->downloadable()
                                    ->disabled()
                                    ->dehydrated()
                                    ->directory('signature')
                                    ->required()
                                    // ->hintAction(
                                    //     Action::make('Delete')
                                    //         ->icon('heroicon-m-trash')
                                    //         // ->visible(fn($state) => filled($this->user['ttd']) || $state)
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
                                    // ->extraAlpineAttributes([
                                    //     'x-on:test.window' => '
                                    //             const pond = FilePond.find($el.querySelector(".filepond--root"));
                                    //             setTimeout(() => {
                                    //                 pond.removeFiles({ revert: false });
                                    //                 pond.addFile($event.detail);
                                    //             }, 750);',
                                    // ])
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
                    ->label(__('photo'))
                    ->circular(),
                TextColumn::make('full_name')
                    ->label(__('full_name'))
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('gender')),
                TextColumn::make('category')
                    ->label(__('category')),
                TextColumn::make('previous_school')
                    ->label(__('previous_school')),
                TextColumn::make('updated_at')
                    ->label(__('updated_at')),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Regular' => 'Regular',
                        'Inklusi' => 'Inklusi',
                    ])
                    ->label(__('category')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                ActionsAction::make('download')
                    ->url(fn(Student $record): string => route('download', $record))
                    ->openUrlInNewTab(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
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
