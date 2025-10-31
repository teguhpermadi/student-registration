<?php

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
        Schema::table('students', function (Blueprint $table) {
            $table->string('guardian_nik')->nullable()->after('mother_phone');
            $table->string('guardian_name')->nullable()->after('guardian_nik');
            $table->string('guardian_city_born')->nullable()->after('guardian_name');
            $table->date('guardian_birthday')->nullable()->after('guardian_city_born');
            $table->string('guardian_religion')->nullable()->after('guardian_birthday');
            $table->string('guardian_education')->nullable()->after('guardian_religion');
            $table->string('guardian_relation')->nullable()->after('guardian_education');
            $table->string('guardian_job')->nullable()->after('guardian_relation');
            $table->string('guardian_income')->nullable()->after('guardian_job');
            $table->string('guardian_phone')->nullable()->after('guardian_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'guardian_nik',
                'guardian_name',
                'guardian_city_born',
                'guardian_birthday',
                'guardian_religion',
                'guardian_education',
                'guardian_relation',
                'guardian_job',
                'guardian_income',
                'guardian_phone',
            ]);
        });
    }
};
