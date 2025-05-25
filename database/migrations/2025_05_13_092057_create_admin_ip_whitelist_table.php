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
        Schema::create('admin_ip_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->comment('IPv4 或 IPv6 字串格式，例如 192.168.1.1');
            $table->string('description')->nullable()->comment('可選備註，例如：公司後台、VPN');
            $table->foreignId('admin_user_id')->constrained('admin_users')->comment('哪位管理員新增的');
            $table->softDeletes();
            $table->timestamps();
            
            // 創建複合 unique 約束，考慮軟刪除
            $table->unique(['ip_address', 'deleted_at'], 'admin_ip_whitelists_ip_address_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ip_whitelists');
    }
};
