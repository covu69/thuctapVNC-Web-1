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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            $table->unsignedBigInteger('id_nsx');
            $table->foreign('id_nsx')->references('id')->on('nhasanxuat');

            $table->unsignedBigInteger('id_nhomthuoc');
            $table->foreign('id_nhomthuoc')->references('id')->on('nhomthuoc');

            $table->json('hoatchat')->nullable(); // Thêm trường JSON mới
            $table->tinyInteger('status')->default(0);
            $table->string('name');
            $table->string('quantity');
            $table->string('unit');
            $table->string('price');
            $table->string('nuoc_sx');
            $table->string('thong_tin');
            $table->timestamps();
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
