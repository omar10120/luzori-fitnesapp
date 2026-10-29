<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workout;
use App\Http\Resources\WorkoutResource;
use App\Http\Resources\WorkoutDetailResource;
use App\Models\UserFavouriteWorkout;
use App\Models\WorkoutDay;
use App\Http\Resources\WorkoutDayResource;
use App\Models\WorkoutDayExercise;
use App\Http\Resources\WorkoutDayExerciseResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    public function getList(Request $request)
    {
        $workout = Workout::active()
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            })
            ->showAssignPrivateWorkout();

        $workout->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });

        $workout->when(request('level_id'), function ($q) {
            $level_ids = explode(',', request('level_id'));
            return $q->whereIn('level_id', $level_ids);
        });

        $workout->when(request('level_ids'), function ($q) {
            $level_ids = explode(',', request('level_ids'));
            return $q->whereIn('level_id', $level_ids);
        });

        $workout->when(request('workout_type_id'), function ($q) {
            return $q->where('workout_type_id', request('workout_type_id'));
        });

        $workout->when(request('workout_type_ids'), function ($q) {
            $workout_type_ids = explode(',', request('workout_type_ids'));
            return $q->whereIn('workout_type_id', $workout_type_ids);
        });
        
        $workout->when(request('eqiupment_ids'), function ($query) {
            return $query->whereHas('workoutExercise',function ($q1) {
                $q1->whereHas('exercise', function ($q) {
                    $equipment_ids = explode(',', request('equipment_ids'));
                    $q->whereIn('equipment_id', $equipment_ids);
                });
            });
        });

        if( $request->has('is_premium') && isset($request->is_premium) ) {
            $workout = $workout->where('is_premium', request('is_premium'));
        }
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $workout->count();
            }
        }

        $workout = $workout->orderBy('title', 'asc')->paginate($per_page);

        $items = WorkoutResource::collection($workout);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }

    public function getDetail(Request $request)
    {
        $workout = Workout::where('id',request('id'))->showAssignPrivateWorkout()->first();
           
        if( $workout == null )
        {
            return json_message_response( __('message.not_found_entry',['name' => __('message.workout') ]) );
        }

        $workout_data = new WorkoutDetailResource($workout);

        $response = [
            'data' => $workout_data,
            'workoutday' => $workout->workoutDay ?? [],
        ];
             
        return json_custom_response($response);
    }

    public function storeCustomWorkout(Request $request)
    {
        $validated = $this->validateCustomWorkoutRequest($request);

        $workout = DB::transaction(function () use ($validated) {
            $workout = Workout::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'slug' => $this->uniqueWorkoutSlug($validated['title']),
                'description' => $validated['description'] ?? null,
                'level_id' => $validated['level_id'],
                'workout_type_id' => $validated['workout_type_id'],
                'status' => 'active',
                'visibility' => 'private',
            ]);

            $this->replaceWorkoutDays($workout, $validated['days']);

            return $workout->load('workoutDay.workoutDayExercise');
        });

        return json_custom_response([
            'message' => __('message.save_form', ['form' => __('message.workout')]),
            'data' => new WorkoutDetailResource($workout),
        ]);
    }

    public function updateCustomWorkout(Request $request)
    {
        $validated = $this->validateCustomWorkoutRequest($request, true);

        $workout = Workout::where('user_id', auth()->id())->find($validated['id']);
        if (!$workout) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.workout')]), 404);
        }

        $workout = DB::transaction(function () use ($workout, $validated) {
            $workout->update(array_intersect_key($validated, array_flip([
                'title',
                'description',
                'level_id',
                'workout_type_id',
            ])));

            $workout->workoutDay()->delete();
            $this->replaceWorkoutDays($workout, $validated['days']);

            return $workout->load('workoutDay.workoutDayExercise');
        });

        return json_custom_response([
            'message' => __('message.update_form', ['form' => __('message.workout')]),
            'data' => new WorkoutDetailResource($workout),
        ]);
    }

    public function destroyCustomWorkout(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $workout = Workout::where('user_id', auth()->id())->find($request->id);
        if (!$workout) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.workout')]), 404);
        }

        $workout->delete();

        return json_message_response(__('message.delete_form', ['form' => __('message.workout')]));
    }

    private function validateCustomWorkoutRequest(Request $request, bool $updating = false): array
    {
        $rules = $this->customWorkoutRules($updating);
        if ($updating) {
            $rules = ['id' => ['required', 'integer'], ...$rules];
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request) {
            $days = $request->input('days', []);
            if (!is_array($days)) {
                return;
            }

            foreach ($days as $index => $day) {
                if (
                    is_array($day)
                    && array_key_exists('is_rest', $day)
                    && !filter_var($day['is_rest'], FILTER_VALIDATE_BOOLEAN)
                    && !array_key_exists('exercises', $day)
                ) {
                    $validator->errors()->add(
                        "days.{$index}.exercises",
                        __('validation.required', ['attribute' => "days.{$index}.exercises"])
                    );
                }
            }
        });

        return $validator->validate();
    }

    private function customWorkoutRules(bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return [
            'title' => [$required, 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'level_id' => [$required, 'integer', 'exists:levels,id'],
            'workout_type_id' => [$required, 'integer', 'exists:workout_types,id'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['array:is_rest,exercises'],
            'days.*.is_rest' => ['required', 'boolean'],
            'days.*.exercises' => ['sometimes', 'array'],
            'days.*.exercises.*' => ['array:exercise_id,sets'],
            'days.*.exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'days.*.exercises.*.sets' => ['nullable', 'array'],
        ];
    }

    private function replaceWorkoutDays(Workout $workout, array $days): void
    {
        foreach ($days as $daySequence => $dayData) {
            $day = $workout->workoutDay()->create([
                'sequence' => $daySequence,
                'is_rest' => $dayData['is_rest'],
            ]);

            if ($dayData['is_rest']) {
                continue;
            }

            foreach ($dayData['exercises'] ?? [] as $sequence => $exerciseData) {
                $day->workoutDayExercise()->create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'sets' => isset($exerciseData['sets'])
                        ? json_encode($exerciseData['sets'], JSON_THROW_ON_ERROR)
                        : null,
                    'sequence' => $sequence,
                ]);
            }
        }
    }

    private function uniqueWorkoutSlug(string $title): string
    {
        $slug = Str::slug($title);
        $baseSlug = $slug !== '' ? $slug : 'workout';
        $suffix = 1;

        while (Workout::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }

    public function getUserFavouriteWorkout(Request $request)
    {
        $workout = Workout::myWorkout()->showAssignPrivateWorkout();

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)) {
            if(is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ) {
                $per_page = $workout->count();
            }
        }

        $workout = $workout->orderByMyFavouriteDesc()->paginate($per_page);

        $items = WorkoutResource::collection($workout);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }

    public function userFavouriteWorkout(Request $request)
    {
        $user_id = auth()->id();
        $workout_id = $request->workout_id;

        $workout = Workout::where('id', $workout_id )->showAssignPrivateWorkout()->first();
        if( $workout == null )
        {
            return json_message_response( __('message.not_found_entry',['name' => __('message.workout') ]) );
        }
        $user_favourite_workout = UserFavouriteWorkout::where('user_id', $user_id)->where('workout_id', $workout_id)->first();
        
        if($user_favourite_workout != null) {
            $user_favourite_workout->delete();
            $message = __('message.unfavourite_workout_list');
        } else {
            $data = [
                'user_id'      => $user_id,
                'workout_id'   => $workout_id,
            ];
            
            UserFavouriteWorkout::create($data);
            $message = __('message.favourite_workout_list');
        }

        return json_message_response($message);
    }

    public function workoutDayList(Request $request)
    {
        $workoutday = WorkoutDay::where('workout_id',request('workout_id'));
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $workoutday->count();
            }
        }

        $workoutday = $workoutday->paginate($per_page);

        $items = WorkoutDayResource::collection($workoutday);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }
    
    public function workoutDayExerciseList(Request $request)
    {
        $day_exercise = WorkoutDayExercise::where('workout_day_id',request('workout_day_id'));
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $day_exercise->count();
            }
        }

        $day_exercise = $day_exercise->paginate($per_page);

        $items = WorkoutDayExerciseResource::collection($day_exercise);

        $response = [
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];
        
        return json_custom_response($response);
    }
}
