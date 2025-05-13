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
        Schema::create('currency_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('幣別代碼，例如 USDT, JPY, KRW, SGD');
            $table->string('name')->comment('幣別中文名稱');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_codes');
    }
};
