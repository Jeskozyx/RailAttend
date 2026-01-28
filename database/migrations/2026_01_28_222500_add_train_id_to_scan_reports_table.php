<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add column without foreign key constraint
        Schema::table('scan_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('train_id')->nullable()->after('schedule_id');
        });

        // Step 2: Populate train_id from existing schedules
        DB::statement('UPDATE scan_reports sr JOIN schedules s ON sr.schedule_id = s.id SET sr.train_id = s.train_id');

        // Step 3: Add foreign key constraint
        Schema::table('scan_reports', function (Blueprint $table) {
            $table->foreign('train_id')->references('id')->on('trains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scan_reports', function (Blueprint $table) {
            $table->dropForeign(['train_id']);
            $table->dropColumn('train_id');
        });
    }
};
