<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nota_penjualans', function (Blueprint $table) {
            $table->date('tanggal_kirim')->nullable();
            $table->string('nomor_po')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nota_penjualans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kirim', 'nomor_po']);
        });
    }
};
