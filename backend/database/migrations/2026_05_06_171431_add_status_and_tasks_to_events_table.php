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
        Schema::table('events', function (Blueprint $table) {
            // Actualitzem l'estat per tenir valors més professionals
            $table->string('status')->default('confirmed')->change();
            // Afegim el camp JSON per les tasques (checklist)
            $table->json('tasks')->nullable()->after('estimated_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('tasks');
        });
    }
};
