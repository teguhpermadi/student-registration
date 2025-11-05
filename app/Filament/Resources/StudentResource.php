<?php

namespace App\Filament\Resources;

use App\CategoryEnum;
use App\ChildRelationEnum;
use App\EducationEnum;
use App\Filament\Exports\StudentExporter;
use App\IncomeEnum;
use App\JobEnum;
use App\Models\Student;
use App\Models\User;
use App\ReligionEnum;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\AcademicYear;
use App\ParentStatusEnum;
use CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert;
use Filament\Forms;
use Filament\Forms\Components\Actions;
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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\UploadedFile;
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

    // order in navigation
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('student_registration');
    }

    protected static ?string $title = 'Siswa';

    protected static ?string $breadcrumb = 'Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SimpleAlert::make('instruction')
                    ->title('Info')
                    ->columnSpanFull()
                    // ->description('Sebelum mengisi formulir berikut ini pastikan anda sudah memiliki: <br/> Pas foto 3 x 4, Scan Akta Lahir, Scan Kartu Keluarga, Scan KTP Ayah, Scan KTP Ibu, Scan Kartu NISN')
                    ->description(fn() => new HtmlString('<p>
                                    Sebelum mengisi formulir berikut ini pastikan anda sudah memiliki: <br/> 
                                    <ol>
                                        <li>1. Pas foto 3 x 4</li>
                                        <li>2. Scan Akta Lahir</li>
                                        <li>3. Scan Kartu Keluarga</li>
                                        <li>4. Scan KTP Ayah</li>
                                    <li>5. Scan KTP Ibu</li>
                                    <li>6. Scan Kartu NISN</li>
                                </ol>
                            </p>'))
                    ->border()
                    ->info(),
                SimpleAlert::make('quota_regular')
                    ->title('Kuota Regular sudah terpenuhi')
                    ->border()
                    ->visible(function (Get $get) {
                        // jika academic year id dipilih
                        if ($get('academic_year_id')) {
                            $academic = AcademicYear::find($get('academic_year_id'));
                            $studentRegular = Student::where('academic_year_id', $get('academic_year_id'))
                                ->where('category', 'Regular')
                                ->count();
                            // periksa jumlah siswa dan quota
                            if ($studentRegular == $academic->quota_regular) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    })
                    ->reactive()
                    ->warning(),
                SimpleAlert::make('quota_inklusi')
                    ->title('Kuota Inklusi sudah terpenuhi')
                    ->border()
                    ->visible(function (Get $get) {
                        // jika academic year id dipilih
                        if ($get('academic_year_id')) {
                            $academic = AcademicYear::find($get('academic_year_id'));
                            $studentInklusi = Student::where('academic_year_id', $get('academic_year_id'))
                                ->where('category', 'Inklusi')
                                ->count();
                            // periksa jumlah siswa dan quota
                            if ($studentInklusi == $academic->quota_inklusi) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    })
                    ->reactive()
                    ->warning(),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Section::make('Formulir Siswa Baru')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->options(AcademicYear::all()->pluck('year', 'id'))
                            ->label(__('academic_year_id'))
                            ->reactive()
                            ->disabled()
                            ->default(AcademicYear::active()->first()->id)
                            ->required(),
                        Select::make('category')
                            ->options(function (Get $get) {
                                $option = [];
                                if ($get('academic_year_id')) {
                                    $academic = AcademicYear::find($get('academic_year_id'));
                                    $studentRegular = Student::where('academic_year_id', $get('academic_year_id'))
                                        ->where('category', 'Regular')
                                        ->count();
                                    $studentInklusi = Student::where('academic_year_id', $get('academic_year_id'))
                                        ->where('category', 'Inklusi')
                                        ->count();

                                    if ($studentRegular < $academic->quota_regular) {
                                        $option['Regular'] = 'Regular';
                                    }

                                    if ($studentInklusi < $academic->quota_inklusi) {
                                        $option['Inklusi'] = 'Inklusi';
                                    }
                                }

                                return $option;
                            })
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
                        TextInput::make('hobby')
                            ->label(__('hobby'))
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
                            ->imageEditor()
                            ->label(__('photo'))
                            ->optimize('jpeg')
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
                        Select::make('father_status')
                            ->label(__('father_status'))
                            ->options(ParentStatusEnum::class)
                            ->default(ParentStatusEnum::Alive->value)
                            ->reactive()
                            ->required(),
                        TextInput::make('father_nik')
                            ->label(__('father_nik'))
                            ->reactive()
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('father_name')
                            ->label(__('father_name'))
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        DatePicker::make('father_birthday')
                            ->label(__('father_birthday'))
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('father_city_born')
                            ->label(__('father_city_born'))
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Select::make('father_religion')
                            ->label(__('father_religion'))
                            ->options(ReligionEnum::class)
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Select::make('father_education')
                            ->label(__('father_education'))
                            ->options(EducationEnum::class)
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('father_relation')
                            ->label(__('father_relation'))
                            ->options(ChildRelationEnum::class)
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('father_job')
                            ->label(__('father_job'))
                            ->options(JobEnum::class)
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('father_income')
                            ->label(__('father_income'))
                            ->options(IncomeEnum::class)
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('father_phone')
                            ->prefix('+62')
                            ->numeric()
                            ->label(__('father_phone'))
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                    ]),
                Section::make('Identitas Ibu')
                    ->columns(2)
                    ->schema([
                        Select::make('mother_status')
                            ->label(__('mother_status'))
                            ->options(ParentStatusEnum::class)
                            ->default(ParentStatusEnum::Alive->value)
                            ->reactive()
                            ->required(),
                        TextInput::make('mother_nik')
                            ->label(__('mother_nik'))
                            ->reactive()
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('mother_name')
                            ->label(__('mother_name'))
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        DatePicker::make('mother_birthday')
                            ->label(__('mother_birthday'))
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('mother_city_born')
                            ->label(__('mother_city_born'))
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Select::make('mother_religion')
                            ->label(__('mother_religion'))
                            ->options(ReligionEnum::class)
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Select::make('mother_education')
                            ->label(__('mother_education'))
                            ->options(EducationEnum::class)
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('mother_relation')
                            ->label(__('mother_relation'))
                            ->options(ChildRelationEnum::class)
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('mother_job')
                            ->label(__('mother_job'))
                            ->options(JobEnum::class)
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        Radio::make('mother_income')
                            ->label(__('mother_income'))
                            ->options(IncomeEnum::class)
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        TextInput::make('mother_phone')
                            ->prefix('+62')
                            ->numeric()
                            ->label(__('mother_phone'))
                            ->visible(fn (Get $get) => $get('mother_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                    ]),
                    Section::make('Identitas Wali')
                        ->description(__('guardian_description'))
                        ->columns(2)
                        ->schema([
                            Toggle::make('guardian_status')
                                ->label(__('guardian_status'))
                                ->reactive(),
                            TextInput::make('guardian_nik')
                                ->label(__('guardian_nik'))
                                ->reactive()
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            TextInput::make('guardian_name')
                                ->label(__('guardian_name'))
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            DatePicker::make('guardian_birthday')
                                ->label(__('guardian_birthday'))
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            TextInput::make('guardian_city_born')
                                ->label(__('guardian_city_born'))
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            Select::make('guardian_religion')
                                ->label(__('guardian_religion'))
                                ->options(ReligionEnum::class)
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            Select::make('guardian_education')
                                ->label(__('guardian_education'))
                                ->options(EducationEnum::class)
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            TextInput::make('guardian_relation')
                                ->label(__('guardian_relation'))
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            Select::make('guardian_job')
                                ->label(__('guardian_job'))
                                ->options(JobEnum::class)
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            Radio::make('guardian_income')
                                ->label(__('guardian_income'))
                                ->options(IncomeEnum::class)
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                            TextInput::make('guardian_phone')
                                ->prefix('+62')
                                ->numeric()
                                ->label(__('guardian_phone'))
                                ->visible(fn (Get $get) => $get('guardian_status') === true),
                        ]),
                Section::make('Unggah File')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('scan_akta_lahir')
                            ->openable()
                            ->helperText('Format file gambar')
                            ->directory('akta_lahir')
                            ->image()
                            ->imageEditor()
                            ->required(),
                        FileUpload::make('scan_kartu_keluarga')
                            ->openable()
                            ->helperText('Format file gambar')
                            ->directory('kartu_keluarga')
                            ->image()
                            ->imageEditor()
                            ->required(),
                        FileUpload::make('scan_ktp_ayah')
                            ->openable()
                            ->helperText('Format file gambar')
                            ->directory('ktp')
                            ->imageEditor()
                            ->image()
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        FileUpload::make('scan_ktp_ibu')
                            ->openable()
                            ->helperText('Format file gambar')
                            ->directory('ktp')
                            ->imageEditor()
                            ->image()
                            ->visible(fn (Get $get) => $get('father_status') === ParentStatusEnum::Alive->value)
                            ->required(),
                        FileUpload::make('scan_nisn')
                            ->openable()
                            ->helperText('Format file gambar')
                            ->directory('nisn')
                            ->image()
                            ->imageEditor(),

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
                                    // ->backgroundColor('rgba(0,0,0,0)')  // Background color on light mode
                                    // ->backgroundColorOnDark('#f0a')     // Background color on dark mode (defaults to backgroundColor)
                                    ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                                    ->penColor('#000')                  // Pen color on light mode
                                    ->penColorOnDark('#fff')            // Pen color on dark mode (defaults to penColor)
                                    // ->exportPenColor('#0f0')
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
                                    ->dehydrated()
                                    ->directory('signature')
                                    ->required()
                                    // ->hintAction(
                                    //     Action::make('Delete')
                                    //         ->icon('heroicon-m-trash')
                                    //         // ->visible(fn($state) => filled($this->user['ttd']) || $state)
                                    //         // ->visible(fn($state) => filled($this->user) || $state)
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
                    ->label(__('photo'))
                    ->circular(),
                TextColumn::make('full_name')
                    ->label(__('full_name'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('gender')),
                TextColumn::make('category')
                    ->label(__('category')),
                TextColumn::make('previous_school')
                    ->label(__('previous_school')),
                TextColumn::make('mother_phone')
                    ->label(__('mother_phone'))
                    ->copyable(),
                TextColumn::make('updated_at')
                    ->label(__('updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Regular' => 'Regular',
                        'Inklusi' => 'Inklusi',
                    ])
                    ->label(__('category')),
                SelectFilter::make('academic_year_id')
                    ->options(AcademicYear::all()->pluck('year', 'id'))
                    ->label(__('academic_year'))
                    ->default(AcademicYear::active()->first()->id),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // custom action to download file
                ActionsAction::make('download')
                    ->url(fn(Student $record): string => route('download', $record))
                    ->openUrlInNewTab()
                    ->visible(
                        // hanya tampilkan jika user role == admin
                        auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')
                    ),
                ActionsAction::make('letter')
                    ->url(fn(Student $record): string => route('letter', $record))
                    ->openUrlInNewTab()
                    ->visible(
                        // hanya tampilkan jika user role == admin
                        auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')
                    ),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    ExportBulkAction::make()->exporter(StudentExporter::class),
                ]),
            ])
            ->modifyqueryusing(function (Builder $query) {
                // jika user role == student, maka hanya tampilkan data siswa yang sesuai dengan user yang login
                if (auth()->user()->hasRole('student')) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateDescription("Silahkan klik tombol berikut ini untuk menambahkan data siswa baru.");
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

    public static function createTemporaryFileUploadFromUrl($favicon, $filename): string
    {
        // Step 1: Save the file to a temporary location
        $tempFilePath = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($tempFilePath, $favicon);

        // Step 2: Create a UploadedFile instance
        $mimeType = mime_content_type($tempFilePath);
        $tempFile = new UploadedFile($tempFilePath, basename($filename), $mimeType);
        $path = Storage::put('livewire-tmp', $tempFile);

        // Step 3: Create a TemporaryUploadedFile instance
        $file = TemporaryUploadedFile::createFromLivewire($path);

        return URL::temporarySignedRoute(
            'livewire.preview-file',
            now()->addMinutes(30)->endOfHour(),
            ['filename' => $file->getFilename()]
        );
    }
}
