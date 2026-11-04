<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function getList(Request $request)
    {
        $programs = Program::where('user_id', auth()->id());

        $perPage = config('constant.PER_PAGE_LIMIT');
        if ($request->filled('per_page') && is_numeric($request->per_page)) {
            if ((int) $request->per_page === -1) {
                $perPage = max($programs->count(), 1);
            } elseif ((int) $request->per_page > 0) {
                $perPage = (int) $request->per_page;
            }
        }

        $programs = $programs->orderByDesc('id')->paginate($perPage);
        $items = ProgramResource::collection($programs);

        return json_custom_response([
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ]);
    }

    public function getDetail(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $program = Program::with('days')
            ->where('user_id', auth()->id())
            ->find($request->id);

        if (!$program) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.program')]), 404);
        }

        return json_custom_response([
            'data' => new ProgramResource($program),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $days = $validated['days'] ?? [];
        unset($validated['days']);

        $program = DB::transaction(function () use ($validated, $days) {
            $program = Program::create([
                ...$validated,
                'user_id' => auth()->id(),
            ]);

            $program->days()->createMany($days);

            return $program->load('days');
        });

        return json_custom_response([
            'message' => __('message.save_form', ['form' => __('message.program')]),
            'data' => new ProgramResource($program),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            ...$this->rules(true),
        ]);

        $program = Program::where('user_id', auth()->id())->find($validated['id']);
        if (!$program) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.program')]), 404);
        }

        $daysWereProvided = array_key_exists('days', $validated);
        $days = $validated['days'] ?? [];
        unset($validated['id'], $validated['days']);

        $program = DB::transaction(function () use ($program, $validated, $days, $daysWereProvided) {
            $program->update($validated);

            if ($daysWereProvided) {
                $program->days()->delete();
                $program->days()->createMany($days);
            }

            return $program->load('days');
        });

        return json_custom_response([
            'message' => __('message.update_form', ['form' => __('message.program')]),
            'data' => new ProgramResource($program),
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $program = Program::where('user_id', auth()->id())->find($request->id);
        if (!$program) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.program')]), 404);
        }

        $program->delete();

        return json_message_response(__('message.delete_form', ['form' => __('message.program')]));
    }

    private function rules(bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return [
            'name' => [$required, 'string', 'max:255'],
            'objective' => ['sometimes', 'nullable', 'string', 'max:255'],
            'duration_weeks' => ['sometimes', 'integer', 'min:1'],
            'is_recurring' => ['sometimes', 'boolean'],
            'frequency' => ['sometimes', 'in:daily,weekly,custom'],
            'start_date' => ['sometimes', 'date'],
            'days' => [$updating ? 'sometimes' : 'required', 'array'],
            'days.*' => ['array:week_number,day_of_week,specific_date,type,workout_id,diet_id,water_goal_ml,steps_goal'],
            'days.*.week_number' => ['nullable', 'integer', 'min:1'],
            'days.*.day_of_week' => ['nullable', 'integer', 'between:1,7'],
            'days.*.specific_date' => ['nullable', 'date'],
            'days.*.type' => ['required', 'in:workout,cardio,rest'],
            'days.*.workout_id' => ['nullable', 'integer', 'exists:workouts,id'],
            'days.*.diet_id' => ['nullable', 'integer', 'exists:diets,id'],
            'days.*.water_goal_ml' => ['nullable', 'integer', 'min:0'],
            'days.*.steps_goal' => ['nullable', 'integer', 'min:0'],
        ];
    }
    public function getDayDetail(Request $request)
    {
        $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $program = Program::where('user_id', auth()->id())->find($request->program_id);
        if (!$program) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.program')]), 404);
        }

        $targetDate = \Carbon\Carbon::parse($request->date);
        
        // 1. Calculate the Week Number
        // If the program has a start date, calculate the difference in weeks. 
        // Otherwise, assume it's Week 1.
        $weekNumber = 1;
        if ($program->start_date) {
            $startDate = \Carbon\Carbon::parse($program->start_date);
            if ($targetDate->greaterThanOrEqualTo($startDate)) {
                $weekNumber = floor($startDate->diffInDays($targetDate) / 7) + 1;
            }
        }

        // 2. Find the specific day
        $query = $program->days();

        if ($program->is_recurring) {
            // For weekly recurring (1 = Monday, 7 = Sunday)
            $query->where('week_number', $weekNumber)
                  ->where('day_of_week', $targetDate->dayOfWeekIso);
        } else {
            // For custom non-recurring dates
            $query->where('specific_date', $request->date);
        }

        $programDay = $query->first();

        if (!$programDay) {
            return json_custom_response([
                'message' => 'No scheduled activity for this day.',
                'data' => null
            ]);
        }

       return json_custom_response([
            'data' => new \App\Http\Resources\ProgramDayResource(
                $programDay->load([
                    'workout.workoutDay.workoutDayExercise.exercise', 
                    'diet'
                ])
            ),
        ]);

    }
}
