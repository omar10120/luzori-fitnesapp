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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('activity')->nullable();
            $table->string('goal')->nullable();
            $table->string('macro_type')->default('balanced')->comment('balanced, low_fat, high_protein, high_carb, keto, custom');
            $table->unsignedTinyInteger('protein_pct')->nullable();
            $table->unsignedTinyInteger('carbs_pct')->nullable();
            $table->unsignedTinyInteger('fat_pct')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['activity', 'goal', 'macro_type', 'protein_pct', 'carbs_pct', 'fat_pct']);
        });
    }
};
