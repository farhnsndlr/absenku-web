<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attendance_records', 'supporting_document_path')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->string('supporting_document_path', 2048)->nullable()->after('photo_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance_records', 'supporting_document_path')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('supporting_document_path');
            });
        }
    }
};
