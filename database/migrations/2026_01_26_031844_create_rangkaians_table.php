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
        Schema::create('rangkaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('train_id');
            $table->string('code');
            $table->string('name');
            $table->timestamps();

            $table->foreign('train_id')->references('id')->on('trains')->onDelete('cascade');
            $table->unique(['train_id', 'code']);
            $table->index(['train_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rangkaian');
    }
};
