<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'session_type')) {
                $table->string('session_type', 10)->default('offline')->after('end_time');
            }
            if (!Schema::hasColumn('courses', 'academic_year')) {
                $table->string('academic_year', 20)->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'session_type')) {
                $table->dropColumn('session_type');
            }
            if (Schema::hasColumn('courses', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
        });
    }
};
