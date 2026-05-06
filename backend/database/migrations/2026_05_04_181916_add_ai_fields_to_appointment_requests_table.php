<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $blueprint) {
            // El camp 'status' ja existeix, així que només afegim els camps d'IA
            $blueprint->decimal('ai_estimated_budget', 15, 2)->nullable()->after('status');
            $blueprint->json('ai_recommendations')->nullable()->after('ai_estimated_budget');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['ai_estimated_budget', 'ai_recommendations']);
        });
    }
};
