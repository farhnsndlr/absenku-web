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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            // 1. ID Sesi (Sesuai model lama: session_id)
            $table->foreignId('session_id')
                ->constrained('attendance_sessions')
                ->onDelete('cascade');

            // 2. ID Mahasiswa (Sesuai model lama: merujuk ke student_profiles)
            $table->foreignId('student_id')
                ->constrained('student_profiles') // Referensi ke tabel profil mahasiswa
                ->onDelete('cascade');

            // 3. Waktu & Status
            $table->dateTime('submission_time'); // Sesuai model lama
            $table->enum('status', ['present', 'late', 'permit', 'sick', 'absent']);

            // 4. Fitur Tambahan (Bisa nullable dulu)
            $table->string('photo_path', 2048)->nullable(); // URL Cloudinary
            $table->string('location_maps')->nullable();   // Lat,Long string
            $table->enum('learning_type', ['online', 'onsite'])->default('onsite'); // Default onsite

            $table->timestamps();

            // (Opsional) Mencegah satu mahasiswa absen 2x di sesi yang sama
            $table->unique(['session_id', 'student_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
