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
        Schema::create('package_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');

            // All columns from the `exercises` table that you want to override
            $table->string('title')->nullable();
            $table->text('instruction')->nullable();
            $table->text('tips')->nullable();
            $table->string('video_type')->nullable();
            $table->text('video_url')->nullable();
            $table->text('bodypart_ids')->nullable();
            $table->string('duration')->nullable();
            $table->string('based')->comment('reps, time')->nullable();
            $table->string('type')->comment('sets, duration')->nullable();
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->json('sets')->nullable();
            $table->integer('seconds_per_rep')->nullable();
            $table->string('status')->nullable()->default('active');
            $table->boolean('is_premium')->default(0)->comment('0-free, 1-premium')->nullable();

            $table->timestamps();

     
            $table->unique(['package_id', 'exercise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_exercises');
    }
};
