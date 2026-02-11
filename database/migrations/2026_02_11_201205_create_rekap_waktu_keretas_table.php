<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_waktu_kereta', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke data mentah
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('scan_report_id')->constrained()->onDelete('cascade'); 
            
            // Data Waktu & Logic
            $table->date('tanggal');               // Tanggal dinas
            $table->dateTime('waktu_awal');        // Scan pertama
            $table->dateTime('waktu_akhir');       // Scan terakhir
            
            $table->integer('durasi_detik');       // Lama keliling (Akhir - Awal)
            $table->integer('jarak_waktu_detik')->default(0); // Jarak dari putaran sebelumnya
            
            $table->integer('sesi_ke')->default(1); // Logic reset 7 jam
            $table->integer('ronde_ke')->default(1); // Putaran ke berapa dalam sesi itu
            
            $table->string('status')->default('normal'); // normal, warning, danger
            
            $table->timestamps();

            // Index biar query filter cepat
            $table->index(['user_id', 'schedule_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_waktu_kereta');
    }
};