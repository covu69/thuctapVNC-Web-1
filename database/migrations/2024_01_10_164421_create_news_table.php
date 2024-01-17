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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->string('thumnail')->nullable();
            $table->text('mo_ta');
            $table->date('ngay_cong_khai');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('noi_bat')->default(0);
            $table->tinyInteger('top_news')->default(0);
            $table->text('noi_dung');
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
