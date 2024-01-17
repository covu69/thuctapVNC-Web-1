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
        Schema::create('hang_thanh_vien', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('thumnail');
            $table->string('muctien');
            $table->tinyInteger('status')->default(0);
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hang_thanh_vien');
    }
};
