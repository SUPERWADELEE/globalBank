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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('order_number')->unique()->comment('交易編號，如 TX20250513001');
            $table->string('type')->comment('deposit / withdraw / exchange');
            $table->foreignId('currency_code_id')->constrained('currency_codes');
            $table->decimal('amount', 20, 6);
            $table->string('status')->comment('pending / completed / failed');
            $table->string('tx_hash')->nullable();
            $table->unsignedBigInteger('related_order_id')->nullable()->comment('可連結 deposit/withdraw/exchange 的 id');
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
