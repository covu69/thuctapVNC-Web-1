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
        Schema::table('product', function (Blueprint $table) {
            $table->string('thumbnail');
            $table->tinyInteger('combo')->default(0);
            $table->string('cangnang');
            $table->string('quy_cach_dong_goi')->nullable();
            $table->string('coin')->nullable();
            $table->string('khuyen_mai')->nullable();
            $table->string('sl_km')->nullable();
            $table->string('sp_km')->nullable();
            $table->date('ngay_bat_dau_khuyen_mai')->nullable(); 
            $table->date('ngay_ket_thuc_khuyen_mai')->nullable();
            $table->date('ngay_het_han')->nullable(); 
            $table->tinyInteger('subdomain')->default(0)->nullable();
            $table->string('url')->nullable();
            $table->text('chi_dinh')->nullable();
            $table->text('cach_dung')->nullable();
            $table->text('chong_chi_dinh')->nullable();
            $table->text('tuong_tac')->nullable();
            $table->text('bao_quan')->nullable();
            $table->text('qua_lieu')->nullable();
            $table->string('sl_toi_thieu')->nullable();
            $table->string('sl_toi_da')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
