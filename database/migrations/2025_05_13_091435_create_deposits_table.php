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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('currency_code_id')->constrained('currency_codes');
            $table->decimal('amount', 20, 6);
            $table->string('tx_hash')->comment('鏈上入金的交易哈希');
            $table->string('status')->comment('pending / approved / rejected / completed');
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->comment('審核人，非必要');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
