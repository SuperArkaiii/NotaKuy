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
        Schema::table('nota_penjualans', function (Blueprint $table) {
            $table->foreignId('data_pelanggan_id')->nullable()->constrained('data_pelanggans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_penjualans', function (Blueprint $table) {
            //
        });
    }
};
