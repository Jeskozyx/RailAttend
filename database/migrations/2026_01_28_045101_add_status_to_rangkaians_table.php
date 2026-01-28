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
    Schema::table('rangkaians', function (Blueprint $table) {
        // 0 = Belum, 1 = Sudah
        $table->boolean('is_verified')->default(false)->after('qr_code'); 
        $table->timestamp('verified_at')->nullable()->after('is_verified');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rangkaians', function (Blueprint $table) {
            //
        });
    }
};
