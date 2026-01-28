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
        Schema::create('scan_reports', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke jadwal dinasan yang sedang dijalankan
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
        
        // Menghubungkan ke kondektur yang bertugas
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // Status laporan: 'process' (sedang scan) atau 'completed' (sudah submit)
        $table->enum('status', ['process', 'completed'])->default('process');
        
        // Catatan tambahan jika ada kendala saat scan
        $table->text('notes')->nullable();
        
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_reports');
    }
};
