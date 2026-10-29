<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_days', function (Blueprint $table) {
            $table->id();
            
            // Link to the main program
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            
            // Scheduling fields (Nullable because a day is either Weekly or Specific Date)
            $table->integer('week_number')->nullable(); // e.g., Week 1, 2, 3, 4
            $table->tinyInteger('day_of_week')->nullable(); // 1=Monday, 7=Sunday
            $table->date('specific_date')->nullable(); // For non-recurring custom dates
            
            // Day Details
            $table->enum('type', ['workout', 'cardio', 'rest'])->default('workout');
            $table->foreignId('workout_id')->nullable()->constrained('workouts')->onDelete('set null');
            $table->foreignId('diet_id')->nullable()->constrained('diets')->onDelete('set null');
            
            // Goals
            $table->integer('water_goal_ml')->nullable(); 
            $table->integer('steps_goal')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_days');
    }
};