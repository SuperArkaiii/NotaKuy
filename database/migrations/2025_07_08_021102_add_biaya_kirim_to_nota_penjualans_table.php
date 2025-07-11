<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('nota_penjualans', function (Blueprint $table) {
        $table->string('biaya_kirim')->nullable(); // Atau tipe data lain yang sesuai
    });
}

public function down()
{
    Schema::table('nota_penjualans', function (Blueprint $table) {
        $table->dropColumn('biaya_kirim');
    });
}

};
