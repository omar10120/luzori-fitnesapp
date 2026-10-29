<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            // Link to the user creating the program (nullable for admin templates)
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            
            // New fields for the updated flow
            $table->string('objective')->nullable()->after('name');
            $table->integer('duration_weeks')->default(4)->after('objective');
            $table->boolean('is_recurring')->default(true)->after('duration_weeks');
            $table->enum('frequency', ['daily', 'weekly', 'custom'])->default('weekly')->after('is_recurring');
        });
    }

    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'objective', 'duration_weeks', 'is_recurring', 'frequency']);
        });
    }
};