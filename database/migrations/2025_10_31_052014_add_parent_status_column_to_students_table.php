<?php

use App\ParentStatusEnum;
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
            $table->string('father_status')->default(ParentStatusEnum::Alive)->after('father_job');
            $table->string('mother_status')->default(ParentStatusEnum::Alive)->after('mother_job');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('father_status');
            $table->dropColumn('mother_status');
        });
    }
};
