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
        $currentYear = date('Y');
        
        Schema::table('academic_years', function (Blueprint $table) use ($currentYear) {
            $table->date('start_date')
                ->after('year')
                ->default("$currentYear-01-01");
            $table->date('end_date')
                ->after('start_date')
                ->default("$currentYear-12-31");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
