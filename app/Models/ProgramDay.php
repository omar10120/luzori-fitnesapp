<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramDay extends Model
{
    protected $fillable = [
        'week_number',
        'day_of_week',
        'specific_date',
        'type',
        'workout_id',
        'diet_id',
        'water_goal_ml',
        'steps_goal',
    ];

    protected $casts = [
        'program_id' => 'integer',
        'week_number' => 'integer',
        'day_of_week' => 'integer',
        'specific_date' => 'date:Y-m-d',
        'workout_id' => 'integer',
        'diet_id' => 'integer',
        'water_goal_ml' => 'integer',
        'steps_goal' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    // ADD THIS
    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    // ADD THIS
    public function diet()
    {
        return $this->belongsTo(Diet::class);
    }
}
