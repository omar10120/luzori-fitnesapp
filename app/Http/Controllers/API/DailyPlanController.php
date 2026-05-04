<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyPlan;
use App\Models\DailyPlanRecipe;
use App\Models\Recipe;
use App\Http\Resources\DailyPlanResource;
use App\Http\Resources\DailyPlanRecipeResource;
use Carbon\Carbon;

class DailyPlanController extends Controller
{
    public function recipeMealTypeResponse($daily_plan, bool $daily_plan_update = false)
    {
        $recipe_meal_type = $daily_plan->meal_type;
        
        $daily_plan_recipe = [];
        
        $totalProtein = $totalFats = $totalCarbs = $totalCalories = 0;

        foreach ( $recipe_meal_type as $meal_type )
        {
            $query_data = [
                'daily_plan_id' => $daily_plan->id,
                'meal_type'     => $meal_type,
            ];
            
            if( $daily_plan_update ) {
                
                $daily_plan_recipe_sum = DailyPlanRecipe::sumProtienCarbsFatsCalories()->planRecipeData($query_data)->where('is_complete', 1)->first();
                
                $totalProtein  += $daily_plan_recipe_sum->total_protein;
                $totalFats     += $daily_plan_recipe_sum->total_fats;
                $totalCarbs    += $daily_plan_recipe_sum->total_carbs;
                $totalCalories += $daily_plan_recipe_sum->total_calories;
            }
            $daily_plan_recipe_query = DailyPlanRecipe::planRecipeData($query_data)->get();
            $daily_plan_recipe[$meal_type] = DailyPlanRecipeResource::collection($daily_plan_recipe_query);
        }

        if( $daily_plan_update ) {
            $daily_plan->update([
                'protein'   => $totalProtein,
                'fats'      => $totalFats,
                'carbs'     => $totalCarbs,
                'calories'  => $totalCalories,
                'eaten'     => $totalCalories,
                'left_eat'  => max(0, $daily_plan->daily_kcal - $totalCalories),
            ]);
        }

        $daily_plan_date = Carbon::parse($daily_plan->date);

        $startOfWeek = $daily_plan_date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = $daily_plan_date->copy()->endOfWeek(Carbon::SUNDAY);
        
        $day_has_daily_plan = DailyPlan::where('user_id', $daily_plan->user_id)->where('eaten', '!=', 0)
            ->whereBetween('date', [ $startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->pluck('date')->toArray();

        $response = [
            'data' => new DailyPlanResource($daily_plan),
            'daily_plan_recipe' => $daily_plan_recipe,
            'day_has_daily_plan' => $day_has_daily_plan,
        ];

        return $response;
    }

    public function saveDailyPlanRecipeData(Request $request)
    {
        $daily_plan = DailyPlan::where('id', request('daily_plan_id'))->first();
        
        if( $daily_plan == null ) {
            return json_message_response( __('message.not_found_entry',['name' => __('message.daily_plan') ]), 400);
        }

        $recipe = Recipe::where('id', request('recipe_id'))->first();
        if( $recipe == null ) {
            return json_message_response( __('message.not_found_entry',['name' => __('message.recipe') ]), 400);
        }

        $data = [
            'daily_plan_id' => request('daily_plan_id'),
            'recipe_id'     => request('recipe_id'),
            'meal_type'     => request('meal_type'),
            'is_complete'   => request('is_complete'),
            'calories'      => $recipe->calories,
            'protein'       => $recipe->protein,
            'fats'          => $recipe->fats,
            'carbs'         => $recipe->carbs,
        ];

        DailyPlanRecipe::updateOrCreate([ 'id' => request('id') ], $data);
                
        $response = $this->recipeMealTypeResponse($daily_plan, true);
        return $response;
    }

    public function getDailyPlanDetail(Request $request)
    {
        $date = request('date') ?? date('Y-m-d');

        $user = auth()->user();
        $daily_plan = DailyPlan::myDailyPlan()->whereDate('date', $date )->first();

        $calculate_daily_plan = DailyPlan::calculateDailyPlan($user);
        
        $daily_kcal = $calculate_daily_plan['kCal'];

        $meal_type = config('macro-nutrient.MEAL_TYPE');
        
        if( $daily_plan == null ) {
            $daily_plan_data = [
                'user_id'   => $user->id,
                'date'      => date('Y-m-d', strtotime($date)),
                'meal_type' => $meal_type,
                'daily_plan'=> $calculate_daily_plan,
                'daily_kcal'=> $daily_kcal,
            ];

            $result = DailyPlan::create($daily_plan_data);

        } else {
            $daily_plan_data = [
                'user_id' => $user->id,
                'date' => date('Y-m-d', strtotime($date)), 
            ];
            $result = DailyPlan::updateOrCreate([ 'id' => $daily_plan->id ], $daily_plan_data);
        }

        $response = $this->recipeMealTypeResponse($result);

        return $response;
    }

    public function deleteDailyPlan(Request $request)
    {
        $daily_plan = DailyPlan::myDailyPlan()->where('id', request('id'))->first();

        $message = __('message.not_found_entry', [ 'name' => __('message.daily_plan') ]);

        if( $daily_plan != null ) {
            $daily_plan->delete();
            $message = __('message.delete_form', [ 'form' => __('message.daily_plan') ]);
        }

        return json_message_response($message);
    }

    public function deleteDailyPlanRecipeData(Request $request)
    {
        $daily_plan_recipe = DailyPlanRecipe::where('id', request('id'))->first();

        $daily_plan = null;
        if( $daily_plan_recipe == null ) {
            $message = __('message.not_found_entry',[ 'name' => __('message.daily_plan_recipe') ]);
            return json_message_response($message);
        }

        if( $daily_plan_recipe != null ) {
            $daily_plan = DailyPlan::where('id', $daily_plan_recipe->daily_plan_id)->first();

            $daily_plan_recipe->delete();
            $message = __('message.delete_form', [ 'name' => __('message.daily_plan_recipe') ]);
        }

        if( $daily_plan != null ) {
            $response = $this->recipeMealTypeResponse($daily_plan, true);
            return $response;
        }
    }

    public function deleteDailyPlanRecipeAllData(Request $request)
    {
        $daily_plan_recipe_exists = DailyPlanRecipe::where('daily_plan_id', request('daily_plan_id'))->exists();

        if( !$daily_plan_recipe_exists )
        {
            $message = __('message.not_found_entry',[ 'name' => __('message.daily_plan_recipe') ]);
            return json_message_response($message);
        }

        if( $daily_plan_recipe_exists )
        {
            $daily_plan = DailyPlan::where('id', request('daily_plan_id'))->first();

            $daily_plan_recipe = DailyPlanRecipe::planRecipeData([ 'daily_plan_id' => $daily_plan->id, 'meal_type' => request('meal_type') ]);
            
            $daily_plan_recipe->delete();
            
            $message = __('message.delete_form', [ 'name' => __('message.daily_plan_recipe') ]);

            $response = $this->recipeMealTypeResponse($daily_plan, true);
            
            return $response;
        }
    }

}
