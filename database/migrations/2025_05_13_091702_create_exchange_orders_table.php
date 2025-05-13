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
        Schema::create('exchange_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('from_currency_id')->constrained('currency_codes');
            $table->foreignId('to_currency_id')->constrained('currency_codes');
            $table->decimal('amount_from', 20, 6);
            $table->decimal('amount_to', 20, 6);
            $table->decimal('rate', 20, 10);
            $table->string('status')->comment('pending / completed / cancelled');
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->comment('操作人，如人工處理');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_orders');
    }
};
