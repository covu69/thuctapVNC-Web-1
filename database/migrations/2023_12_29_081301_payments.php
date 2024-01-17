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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_hang')->unique();
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('dai_ly');
            $table->tinyInteger('device')->nullable();
            $table->string('name');
            $table->string('sdt', 10);
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('mst', 20)->nullable();
            $table->string('ghi_chu')->nullable();
            $table->tinyInteger('payment_method')->default(0);
            $table->string('voucher_code')->nullable();
            $table->tinyInteger('use_coin')->default(0);
            $table->integer('coins')->default(0);
            $table->double('voucher_value')->default(0);
            $table->double('total_price')->nullable();
            $table->tinyInteger('payment_status')->default(0);
            $table->string('transaction_image')->nullable();
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
        //
    }
};
