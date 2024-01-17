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
        Schema::create('dai_ly', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng
            $table->unsignedBigInteger('id_tinh');
            $table->foreign('id_tinh')->references('id')->on('tinhtable');
            $table->unsignedBigInteger('id_nguoi_quan_ly');
            $table->foreign('id_nguoi_quan_ly')->references('id')->on('users');
            $table->string('ten'); 
            $table->string('ten_nha_thuoc'); 
            $table->string('ma_khach_hang')->nullable();
            $table->string('email');
            $table->string('so_dien_thoai'); 
            $table->text('password');
            $table->string('ma_so_thue')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('imgae');
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
