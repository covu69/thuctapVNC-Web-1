<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_payment');
            $table->foreign('id_payment')->references('id')->on('payment')->onDelete('cascade');            
            $table->integer('id_product');
            $table->integer('so_luong');
            $table->double('price');
            $table->string('name_product');
            $table->string('img_product')->nullable();
            $table->double('khuyen_mai')->nullable();
            $table->integer('bonus_coins')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_detail');
    }
};
