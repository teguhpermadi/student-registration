<?php

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId(Student::class);
            $table->string('full_name');
            $table->string('nick_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('city_born');
            $table->date('birthday');
            $table->string('nisn');
            $table->string('nik');
            $table->text('address');
            $table->string('village');
            $table->string('district');
            $table->string('city');
            $table->string('province');
            $table->string('previous_school');
            $table->string('address_previous_school')->nullable();
            $table->string('poscode')->nullable();
            $table->string('father_status')->nullable();
            $table->string('father_nik')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_city_born')->nullable();
            $table->date('father_birthday')->nullable();
            $table->string('father_religion')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_relation')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_income')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_status')->nullable();
            $table->string('mother_nik')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_city_born')->nullable();
            $table->date('mother_birthday')->nullable();
            $table->string('mother_religion')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_relation')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_income')->nullable();
            $table->string('mother_phone')->nullable();
            $table->date('date_received')->nullable();
            $table->string('grade_received')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
