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
        Schema::create('voucher', function (Blueprint $table) {
            $table->id(); 
            $table->string('tieu_de');
            $table->string('ma_giam_gia');
            $table->string('muc_tien');
            $table->string('tong_hoa_don')->nullable();
            $table->date('ngay_bat_dau'); 
            $table->date('ngay_ket_thuc');
            $table->text('noi_dung');
            $table->json('doi_tuong')->nullable();
            $table->tinyInteger('loai')->default(0);
            $table->timestamps(); // Thêm cột created_at và updated_at
            $table->softDeletes(); // Sử dụng hàm softDeletes để thêm cột deleted_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
