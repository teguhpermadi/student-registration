<?php

namespace App\Filament\Pages;

use App\ChildRelationEnum;
use App\EducationEnum;
use App\IncomeEnum;
use App\JobEnum;
use App\Models\Student;
use App\ReligionEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

use function Laravel\Prompts\select;
use function PHPSTORM_META\map;

class StudentRegistration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.student-registration';


    public ?array $data = [];

    public function mount(): void
    {
        $student = auth()->user()->student;
        // dd($student->toArray());
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
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Wizard::make([
                    Step::make('Student Identity')
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
                        ]),
                    Step::make('Address')
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
                    Step::make('Data Previous School')
                        ->columns(2)
                        ->schema([
                            TextInput::make('previous_school')
                                ->translateLabel()
                                ->required(),
                            TextInput::make('address_previous_school')
                                ->translateLabel()
                                ->required(),
                        ]),
                    Step::make('Father Identity')
                        ->columns(2)
                        ->schema([
                            Select::make('father_status')
                                ->options([
                                    'alive' => 'Hidup',
                                    'die' => 'Meninggal dunia'
                                ])
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
                    Step::make('Mother Identity')
                        ->columns(2)
                        ->schema([
                            Select::make('mother_status')
                                ->options([
                                    'alive' => 'Hidup',
                                    'die' => 'Meninggal dunia'
                                ])
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
                    Step::make('File Upload')
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
                ])
                    ->skippable()
                // ->submitAction(new HtmlString(Blade::render(<<<BLADE
                //         <x-filament::button
                //             type="submit"
                //         >
                //             {{__("Submit")}}
                //         </x-filament::button>
                //     BLADE))),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        // dd($this->form->getState()['student_id']);
        $student_id = [
            'user_id' => $this->form->getState()['user_id'],
        ];

        $post = Student::updateOrCreate($student_id, $this->form->getState());

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
