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
        Schema::create('cart', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('dai_ly');
            $table->unsignedBigInteger('id_product');
            $table->foreign('id_product')->references('id')->on('product');
            $table->integer('so_luong');
            $table->timestamps(); // Thêm cột created_at và updated_at
            $table->softDeletes(); // Sử dụng hàm softDeletes để thêm cột deleted_at
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
