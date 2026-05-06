<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $blueprint) {
            $blueprint->string('location')->nullable()->after('availability'); // Ciutat/Zona
            $blueprint->json('specialization_tags')->nullable()->after('location'); // ['Sound', 'Lighting', ...]
            $blueprint->float('rating', 3, 2)->default(5.00)->after('specialization_tags'); // Puntuació IA/Admin
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['location', 'specialization_tags', 'rating']);
        });
    }
};
