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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('hari');
            $table->unsignedBigInteger('guru_id');
            $table->string('kelas_id');
            $table->unsignedBigInteger('mapel_id');

            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('restrict');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('restrict');
            $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
