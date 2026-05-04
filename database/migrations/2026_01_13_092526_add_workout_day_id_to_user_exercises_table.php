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
        Schema::table('user_exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('workout_day_id')->nullable()->after('workout_id');
            $table->foreign('workout_day_id')->references('id')->on('workout_days')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_exercises', function (Blueprint $table) {
            $table->dropForeign(['workout_day_id']);
            $table->dropColumn('workout_day_id');
        });
    }
};
