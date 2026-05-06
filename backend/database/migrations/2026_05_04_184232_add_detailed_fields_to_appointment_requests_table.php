<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $blueprint) {
            $blueprint->string('start_time')->nullable()->after('desired_date');
            $blueprint->string('end_time')->nullable()->after('start_time');
            $blueprint->string('location_name')->nullable()->after('end_time');
            $blueprint->boolean('wants_music')->default(false)->after('location_name');
            $blueprint->string('music_type')->nullable()->after('wants_music');
            $blueprint->string('menu_type')->nullable()->after('music_type');
            $blueprint->text('dietary_requirements')->nullable()->after('menu_type');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'start_time', 'end_time', 'location_name', 
                'wants_music', 'music_type', 'menu_type', 'dietary_requirements'
            ]);
        });
    }
};
