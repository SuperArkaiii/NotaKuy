<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nota_penjualans', function (Blueprint $table) {
            $table->string('kode_faktur')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nota_penjualans', function (Blueprint $table) {
            $table->string('kode_faktur')->nullable(false)->change();
        });
    }
};

