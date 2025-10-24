<?php

namespace Database\Seeders;

use App\CategoryEnum;
use App\ChildRelationEnum;
use App\EducationEnum;
use App\IncomeEnum;
use App\JobEnum;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;
use App\ReligionEnum;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker::create('id_ID');
    }

    public function run(): void
    {
        // Get or create academic year
        $academicYear = AcademicYear::first();
        
        if (!$academicYear) {
            $academicYear = AcademicYear::create([
                'year' => now()->year . '/' . (now()->year + 1),
                'status' => 'active',
                'quota_regular' => 100,
                'quota_inklusi' => 50,
            ]);
        }
        
        // Create 10 random students
        for ($i = 0; $i < 10; $i++) {
            $this->createStudent($academicYear->id);
        }
    }

    private function generateDateRange(Carbon $startDate, Carbon $endDate, string $interval = 'day'): array
    {
        $dates = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dates[] = $current->copy();
            $current->add($interval);
        }

        return $dates;
    }
    
    public function __destruct()
    {
        // Clean up any remaining temporary files
        $files = glob(storage_path('app/livewire-tmp/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function createStudent($academicYearId): void
    {
        // Create user first
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Generate random dates within the last 30 days
        $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
        
        // Create sample image files
        $photoPath = 'photo/' . uniqid() . '.jpg';
        Storage::put($photoPath, file_get_contents('https://i.pravatar.cc/300?img=' . rand(1, 70)));
        
        $aktaPath = 'akta_lahir/' . uniqid() . '.jpg';
        Storage::put($aktaPath, file_get_contents('https://picsum.photos/800/1000?random=1'));
        
        $kkPath = 'kartu_keluarga/' . uniqid() . '.jpg';
        Storage::put($kkPath, file_get_contents('https://picsum.photos/800/1000?random=2'));
        
        $ktpAyahPath = 'ktp/' . uniqid() . '.jpg';
        Storage::put($ktpAyahPath, file_get_contents('https://picsum.photos/800/1000?random=3'));
        
        $ktpIbuPath = 'ktp/' . uniqid() . '.jpg';
        Storage::put($ktpIbuPath, file_get_contents('https://picsum.photos/800/1000?random=4'));
        
        $nisnPath = 'nisn/' . uniqid() . '.jpg';
        Storage::put($nisnPath, file_get_contents('https://picsum.photos/800/1000?random=5'));
        
        $ttdPath = 'signature/' . uniqid() . '.png';
        Storage::put($ttdPath, file_get_contents('https://i.imgur.com/6UvJ9fP.png'));
        
        // Create student data
        $studentData = [
            'user_id' => $user->id,
            'academic_year_id' => $academicYearId,
            'category' => $this->faker->randomElement(['Regular', 'Inklusi']),
            'full_name' => $this->faker->name,
            'nick_name' => $this->faker->firstName,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'city_born' => $this->faker->city,
            'birthday' => $this->faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'),
            'hobby' => $this->faker->randomElement(['Membaca', 'Menggambar', 'Olahraga', 'Musik', 'Menari']),
            'nisn' => $this->faker->numerify('############'),
            'nik' => $this->faker->numerify('##############'),
            'number_akta_lahir' => $this->faker->numerify('##########'),
            'number_kartu_keluarga' => $this->faker->numerify('################'),
            'address' => $this->faker->address,
            'village' => 'Kelurahan ' . $this->faker->city,
            'district' => 'Kecamatan ' . $this->faker->city,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'previous_school' => 'SD ' . $this->faker->city,
            'address_previous_school' => $this->faker->address,
            'poscode' => $this->faker->postcode,
            'father_nik' => $this->faker->numerify('##############'),
            'father_name' => $this->faker->name('male'),
            'father_city_born' => $this->faker->city,
            'father_birthday' => $this->faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
            'father_religion' => $this->faker->randomElement(ReligionEnum::cases()),
            'father_education' => $this->faker->randomElement(EducationEnum::cases()),
            'father_relation' => $this->faker->randomElement(ChildRelationEnum::cases()),
            'father_job' => $this->faker->randomElement(JobEnum::cases()),
            'father_income' => $this->faker->randomElement(IncomeEnum::cases()),
            'father_phone' => '08' . $this->faker->numerify('##########'),
            'mother_nik' => $this->faker->numerify('##############'),
            'mother_name' => $this->faker->name('female'),
            'mother_city_born' => $this->faker->city,
            'mother_birthday' => $this->faker->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
            'mother_religion' => $this->faker->randomElement(ReligionEnum::cases()),
            'mother_education' => $this->faker->randomElement(EducationEnum::cases()),
            'mother_relation' => $this->faker->randomElement(ChildRelationEnum::cases()),
            'mother_job' => $this->faker->randomElement(JobEnum::cases()),
            'mother_income' => $this->faker->randomElement(IncomeEnum::cases()),
            'mother_phone' => '08' . $this->faker->numerify('##########'),
            'date_received' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'grade_received' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'photo' => $photoPath,
            'scan_akta_lahir' => $aktaPath,
            'scan_kartu_keluarga' => $kkPath,
            'scan_ktp_ayah' => $ktpAyahPath,
            'scan_ktp_ibu' => $ktpIbuPath,
            'scan_nisn' => $nisnPath,
            'ttd' => $ttdPath,
            'ttd_name' => $this->faker->name,
            'is_resign' => $this->faker->boolean(10), // 10% chance of being true
            'agreement' => true,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        // Create student record
        $student = Student::create($studentData);
        
        // Add random delay to make created_at times more varied
        sleep(1);
    }
}
