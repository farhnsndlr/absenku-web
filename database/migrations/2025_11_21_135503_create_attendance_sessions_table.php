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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('learning_type', ['online', 'offline']);
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->enum('status', ['scheduled', 'open', 'closed'])->default('scheduled');
            $table->string('session_token', 10)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['session_date', 'start_time', 'end_time', 'session_type', 'location_id', 'name']);
        });
    }
};
