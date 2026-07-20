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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('is_follow_up')->after('status')->nullable()->default(0)->comment('0-no, 1-yes');
            $table->integer('food_recognition_limit')->after('is_follow_up')->nullable()->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('is_follow_up');
            $table->dropColumn('food_recognition_limit');
        });
    }
};
