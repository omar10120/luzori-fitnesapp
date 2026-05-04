<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPlanRecipe extends Model
{
    protected $fillable = [ 'daily_plan_id', 'recipe_id', 'meal_type', 'calories', 'protein', 'fats', 'carbs', 'is_complete' ];

     protected $casts = [
        'daily_plan_id' => 'integer',
        'recipe_id'     => 'integer',
        'calories'      => 'double',
        'protein'       => 'double',
        'fats'          => 'double',
        'carbs'         => 'double',
        'is_complete'   => 'boolean',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }

    public function scopeSumProtienCarbsFatsCalories($query)
    {
        return $query->selectRaw('
                    COALESCE(SUM(protein), 0)  as total_protein,
                    COALESCE(SUM(fats), 0)     as total_fats,
                    COALESCE(SUM(carbs), 0)    as total_carbs,
                    COALESCE(SUM(calories), 0) as total_calories
                ');
    }

    public function scopePlanRecipeData($query, $query_data = null)
    {
        $query_data = $query_data ?? [];

        $query->when( !empty($query_data['daily_plan_id']), fn ($q) 
            => $q->where('daily_plan_id', $query_data['daily_plan_id'])
        );

        $query->when( !empty($query_data['meal_type']), fn ($q) 
            => $q->where('meal_type', $query_data['meal_type'])
        );
        
        return $query;
    }

    public function dailyplan()
    {
        return $this->belongsTo(DailyPlan::class, 'daily_plan_id', 'id');
    }
}
