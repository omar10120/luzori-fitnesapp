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
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('diet_id')->nullable()->after('id')->constrained('diets')->onDelete('set null');
            $table->foreignId('advice_id')->nullable()->after('diet_id')->constrained('advice')->onDelete('set null');
            $table->foreignId('exercise_id')->nullable()->after('advice_id')->constrained('exercises')->onDelete('set null');
            $table->string('follow_up_price')->nullable()->after('price');
            $table->string('food_recognition_limit')->nullable()->after('follow_up_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['diet_id']);
            $table->dropForeign(['advice_id']);
            $table->dropForeign(['exercise_id']);
            $table->dropColumn(['diet_id', 'advice_id', 'exercise_id', 'follow_up_price', 'food_recognition_limit']);
        });
    }
};
