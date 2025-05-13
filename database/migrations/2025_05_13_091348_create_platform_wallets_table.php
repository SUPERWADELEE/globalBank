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
        Schema::create('platform_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_code_id')->constrained('currency_codes');
            $table->decimal('amount', 18, 8)->default(0)->comment('平台目前持有的該幣別餘額');
            $table->boolean('is_active')->default(true)->comment('是否啟用該幣別的交易');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_wallets');
    }
};
