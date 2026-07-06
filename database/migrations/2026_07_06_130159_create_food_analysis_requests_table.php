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
        Schema::create('food_analysis_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('caloriemama');
            $table->string('image_path');
            $table->boolean('is_food')->nullable();
            $table->string('top_food_name')->nullable();
            $table->string('top_group')->nullable();
            $table->unsignedInteger('top_score')->nullable();
            $table->decimal('calories', 12, 4)->nullable();
            $table->decimal('protein', 12, 4)->nullable();
            $table->decimal('total_fat', 12, 4)->nullable();
            $table->decimal('total_carbs', 12, 4)->nullable();
            $table->json('response_json')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_analysis_requests');
    }
};
