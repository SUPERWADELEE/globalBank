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
        Schema::table('exchange_orders', function (Blueprint $table) {
            if (Schema::hasColumn('exchange_orders', 'description')) {
                $table->dropColumn('description');
            }
            if (!Schema::hasColumn('exchange_orders', 'created_at')) {
                $table->timestamp('created_at')->useCurrent();
            }

            if (!Schema::hasColumn('exchange_orders', 'updated_at')) {
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_orders', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
    }
};
