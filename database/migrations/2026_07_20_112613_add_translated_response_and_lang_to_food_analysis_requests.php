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
        Schema::table('food_analysis_requests', function (Blueprint $table) {
            $table->json('response_json_ar')->nullable()->after('response_json');
            $table->string('lang', 5)->nullable()->after('status'); // e.g
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_analysis_requests', function (Blueprint $table) {
            $table->dropColumn(['response_json_ar', 'lang']);

        });
    }
};
