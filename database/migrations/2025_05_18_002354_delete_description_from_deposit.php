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
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('deposits', 'tx_hash')) {
                $table->string('tx_hash')->comment('鏈上入金的交易哈希')->nullable()->change();
            }

            if (!Schema::hasColumn('deposits', 'created_at')) {
                $table->timestamp('created_at')->useCurrent();
            }
            if (!Schema::hasColumn('deposits', 'updated_at')) {
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->string('tx_hash')->comment('鏈上入金的交易哈希')->nullable()->change();
        });
    }
};
