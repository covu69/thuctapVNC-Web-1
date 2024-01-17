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
        Schema::table('dai_ly', function (Blueprint $table) {
            $table->unsignedBigInteger('id_hang_tv')->nullable();
            $table->foreign('id_hang_tv')->references('id')->on('hang_thanh_vien');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dai_ly', function (Blueprint $table) {
            //
        });
    }
};
