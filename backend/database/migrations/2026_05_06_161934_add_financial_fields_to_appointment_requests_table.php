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
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->decimal('ai_operational_cost', 15, 2)->nullable()->after('ai_estimated_budget');
            $table->decimal('ai_estimated_profit', 15, 2)->nullable()->after('ai_operational_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dropColumn(['ai_operational_cost', 'ai_estimated_profit']);
        });
    }
};
