<?php

namespace App\Filament\Pages;

use App\CategoryEnum;
use App\ChildRelationEnum;
use App\EducationEnum;
use App\IncomeEnum;
use App\JobEnum;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;
use App\ReligionEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
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
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Laravel\Prompts\select;
use function PHPSTORM_META\map;

class StudentRegistration extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.student-registration';

    public ?array $data = [];
    public $user;

    public function mount(): void
    {
        $student = auth()->user()->student;
        $this->user = $student;
        // dd($this->user);
        if ($student) {
            $this->form->fill($student->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SimpleAlert::make('instruction')
                    ->title('Sebelum mengisi formulir berikut ini pastikan anda sudah memiliki: Pas foto 3 x 4, Scan Akta Lahir, Scan Kartu Keluarga, Scan KTP Ayah, Scan KTP Ibu, Scan Kartu NISN')
                    ->border()
                    ->info(),
                SimpleAlert::make('quota_regular')
                    ->title('Kuota Regular sudah terpenuhi')
                    ->border()
                    ->visible(function(Get $get){
                        // jika academic year id dipilih
                        if ($get('academic_year_id')) {
                            $academic = AcademicYear::find($get('academic_year_id'));
                            $studentRegular = Student::where('academic_year_id', $get('academic_year_id'))
                                        ->where('category', 'Regular')
                                        ->count();
                            // periksa jumlah siswa dan quota
                            if($studentRegular == $academic->quota_regular){
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
                    ->visible(function(Get $get){
                        // jika academic year id dipilih
                        if ($get('academic_year_id')) {
                            $academic = AcademicYear::find($get('academic_year_id'));
                            $studentInklusi = Student::where('academic_year_id', $get('academic_year_id'))
                                        ->where('category', 'Inklusi')
                                        ->count();
                            // periksa jumlah siswa dan quota
                            if($studentInklusi == $academic->quota_inklusi){
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
                Section::make('Formulir PPDB')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->options(AcademicYear::all()->pluck('year', 'id'))
                            ->reactive()
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
                            ->reactive()
                            ->required(),
                    ]),
                Section::make('Student Identity')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->translateLabel()
                            ->dehydrateStateUsing(fn(string $state): string => ucwords($state))
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
                            ->numeric()
                            ->helperText('nomor induk siswa nasional')
                            ->required(),
                        TextInput::make('nik')
                            ->numeric()
                            ->helperText('nomor induk kependudukan siswa')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('number_akta_lahir')
                            ->helperText('nomor akta lahir')
                            ->translateLabel()
                            ->required(),
                        TextInput::make('number_kartu_keluarga')
                            ->helperText('nomor kartu keluarga')
                            ->translateLabel()
                            ->required(),
                        FileUpload::make('photo')
                            ->openable()
                            ->directory('photo')
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
                            ->numeric()
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
                        TextInput::make('father_nik')
                            ->translateLabel()
                            ->reactive()
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
                            ->prefix('+62')
                            ->numeric()
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('Mother Identity')
                    ->columns(2)
                    ->schema([
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
                            ->prefix('+62')
                            ->numeric()
                            ->translateLabel()
                            ->required(),
                    ]),
                Section::make('File Upload')
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
                                SignaturePad::make('signature')
                                    ->label(__('Sign here'))
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
                                    ->label('Signature Result')
                                    ->downloadable()
                                    ->disabled()
                                    ->dehydrated()
                                    ->directory('signature')
                                    ->required()
                                    ->hintAction(
                                        Action::make('Delete')
                                            ->icon('heroicon-m-trash')
                                            // ->visible(fn($state) => filled($this->user['ttd']) || $state)
                                            ->visible(fn($state) => filled($this->user) || $state)
                                            ->requiresConfirmation()
                                            ->action(function ($state, $set) {
                                                if (!empty($this->user['ttd'] ?? null)) {
                                                    Storage::disk('public')->delete($this->user['ttd']);
                                                    $this->user['ttd'] = null;
                                                    $this->user->save();

                                                    return redirect(request()->header('Referer'));
                                                } else {
                                                    $file = reset($state);
                                                    if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                        Storage::delete($file->getPathName());
                                                        $set('ttd', null);
                                                    }
                                                }
                                            })
                                    )
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
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        // dd($this->form->getState());
        $student_id = [
            'user_id' => $this->form->getState()['user_id'],
        ];

        $post = Student::updateOrCreate($student_id, $this->form->getState());

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
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
