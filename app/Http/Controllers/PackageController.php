<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\PackageDataTable;
use App\Models\Package;
use App\Models\PackageExercise;
use App\Models\Diet;
use App\Models\Advice;
use App\Models\Exercise;
use App\Models\User;
use App\Helpers\AuthHelper;
use App\Http\Requests\PackageRequest;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index(PackageDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title',['form' => __('message.package')] );
        $auth_user = AuthHelper::authSession();
        if( !$auth_user->can('package-list') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $assets = ['data-table'];

        $headerAction = $auth_user->can('package-add') ? '<a href="'.route('package.create').'" class="btn btn-sm btn-primary" role="button">'.__('message.add_form_title', [ 'form' => __('message.package')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    public function create()
    {
        if( !auth()->user()->can('package-add') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.package')]);
        $diets = Diet::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $advices = Advice::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $exercises = Exercise::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $users = User::where('status', 'active')->orderBy('username')->pluck('username', 'id');
        $existingPackages = Package::orderBy('name')->pluck('name', 'id'); 

            return view('package.form', compact('pageTitle', 'diets', 'advices', 'exercises', 'users', 'existingPackages'));

    }

    public function store(PackageRequest $request)
    {
        if( !auth()->user()->can('package-add') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        DB::transaction(function () use ($request) {
            // 1. Create the main Package
            $package = Package::create($request->only([
                'name', 'duration_unit', 'duration', 'price', 'description', 'status',
                'diet_id', 'advice_id', 'follow_up_price', 'food_recognition_limit',
                // 'exercise_id' is removed here because it's now in package_exercises
            ]));

            // 2. Handle Media & Users
            if (isset($request->package_image) && $request->package_image != null) {
                storeMediaFile($package, $request->package_image, 'package_image');
            }
            $package->users()->attach($request->users ?? []);

            // 3. Loop through and save multiple exercises
            foreach ($request->exercises as $exData) {
                $exercise = Exercise::findOrFail($exData['exercise_id']);
                
                // Clone the base exercise data
                $packageExercise = $this->cloneExerciseToPackage($package, $exercise);
                
                // Apply the custom overrides from the form
                $this->updatePackageExerciseFromData($packageExercise, $exData);
            }
        });

        return redirect()->route('package.index')->withSuccess(__('message.save_form', ['form' => __('message.package')]));
    }

    public function getExerciseData($id)
    {
        $exercise = Exercise::findOrFail($id);

        $durationParts = $exercise->duration ? explode(':', $exercise->duration) : [null, null, null];

        return response()->json([
            'id'              => $exercise->id,
            'title'           => $exercise->title,
            'instruction'     => $exercise->instruction,
            'tips'            => $exercise->tips,
            'video_type'      => $exercise->video_type,
            'video_url'       => $exercise->video_url,
            'bodypart_ids'    => $exercise->bodypart_ids,
            'duration'        => $exercise->duration,
            'hours'           => $durationParts[0] ?? null,
            'minute'          => $durationParts[1] ?? null,
            'second'          => $durationParts[2] ?? null,
            'based'           => $exercise->based ?? 'reps',
            'type'            => $exercise->type ?? 'sets',
            'equipment_id'    => $exercise->equipment_id,
            'level_id'        => $exercise->level_id,
            'sets'            => $exercise->sets ?? [],
            'status'          => $exercise->status ?? 'active',
            'is_premium'      => (int) $exercise->is_premium,
            'seconds_per_rep' => $exercise->seconds_per_rep,
        ]);
    }

    public function show($id)
    {
        $data = Package::with(['packageExercise', 'exercise', 'diet', 'advice'])->findOrFail($id);

        return redirect()->route('package.edit', $id);
    }

    public function edit($id)
    {
        // Change 'packageExercise' to 'packageExercises'
        $data = Package::with('packageExercises')->findOrFail($id); 
        
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.package') ]);
        $diets = Diet::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $advices = Advice::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $exercises = Exercise::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $users = User::where('status', 'active')->orderBy('username')->pluck('username', 'id');

        return view('package.form', compact('data','id','pageTitle', 'diets', 'advices', 'exercises', 'users'));
    }

    public function update(PackageRequest $request, $id)
    {
        if( !auth()->user()->can('package-edit') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $package = Package::with('packageExercises')->findOrFail($id);

        DB::transaction(function () use ($request, $package) {
            // 1. Update basic package info
            $package->fill($request->only([
                'name', 'duration_unit', 'duration', 'price', 'description', 'status',
                'diet_id', 'advice_id', 'follow_up_price', 'food_recognition_limit',
            ]))->update();

            // 2. Sync users
            $package->users()->sync($request->users ?? []);

            // 3. Update Media
            if (isset($request->package_image) && $request->package_image != null) {
                $package->clearMediaCollection('package_image');
                $package->addMediaFromRequest('package_image')->toMediaCollection('package_image');
            }

            // 4. Sync Exercises
            $existingExercises = $package->packageExercises->keyBy('exercise_id');
            $keptExerciseIds = [];

            foreach ($request->exercises as $exData) {
                $exerciseId = $exData['exercise_id'];
                $exercise = Exercise::findOrFail($exerciseId);

                if (isset($existingExercises[$exerciseId])) {
                    // If exercise already exists in this package, update its overrides
                    $packageExercise = $existingExercises[$exerciseId];
                    $this->updatePackageExerciseFromData($packageExercise, $exData);
                } else {
                    // If it's a brand new exercise added to the package, clone and set overrides
                    $packageExercise = $this->cloneExerciseToPackage($package, $exercise);
                    $this->updatePackageExerciseFromData($packageExercise, $exData);
                }
                
                // Keep track of this ID so we don't delete it in the next step
                $keptExerciseIds[] = $exerciseId;
            }

            // 5. Delete exercises that the admin removed from the form
            $package->packageExercises()->whereNotIn('exercise_id', $keptExerciseIds)->delete();
        });

        return redirect()->route('package.index')->withSuccess(__('message.update_form',['form' => __('message.package')]));
    }

    public function destroy($id)
    {
        if( !auth()->user()->can('package-delete') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $package = Package::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.package')]);

        if($package != '') {
            $package->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.package')]);
        }

        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }

    protected function cloneExerciseToPackage(Package $package, Exercise $exercise): PackageExercise
    {
        if ($package->packageExercise) {
            $package->packageExercise->delete();
        }

        $package->unsetRelation('packageExercise');

        return $package->packageExercise()->create([
            'exercise_id'     => $exercise->id,
            'title'           => $exercise->title,
            'instruction'     => $exercise->instruction,
            'tips'            => $exercise->tips,
            'video_type'      => $exercise->video_type,
            'video_url'       => $exercise->video_url,
            'bodypart_ids'    => $exercise->bodypart_ids,
            'duration'        => $exercise->duration,
            'based'           => $exercise->based,
            'type'            => $exercise->type,
            'equipment_id'    => $exercise->equipment_id,
            'level_id'        => $exercise->level_id,
            'sets'            => $exercise->sets,
            'status'          => $exercise->status,
            'is_premium'      => $exercise->is_premium,
            'seconds_per_rep' => $exercise->seconds_per_rep,
        ]);
    }

    protected function updatePackageExerciseFromRequest(PackageExercise $packageExercise, PackageRequest $request): void
    {
        $type = $request->input('pe_type', $packageExercise->type);
        $payload = [
            'title'           => $request->input('pe_title', $packageExercise->title),
            'instruction'     => $request->input('pe_instruction', $packageExercise->instruction),
            'tips'            => $request->input('pe_tips', $packageExercise->tips),
            'video_type'      => $request->input('pe_video_type', $packageExercise->video_type),
            'video_url'       => $request->input('pe_video_url', $packageExercise->video_url),
            'equipment_id'    => $request->input('pe_equipment_id', $packageExercise->equipment_id),
            'level_id'        => $request->input('pe_level_id', $packageExercise->level_id),
            'status'          => $request->input('pe_status', $packageExercise->status),
            'is_premium'      => $request->has('pe_is_premium') ? (int) $request->pe_is_premium : $packageExercise->is_premium,
            'type'            => $type,
            'bodypart_ids'    => $request->has('pe_bodypart_ids') ? $request->pe_bodypart_ids : $packageExercise->bodypart_ids,
        ];

        if ($type === 'duration') {
            if ($request->filled('pe_hours') && $request->filled('pe_minute') && $request->filled('pe_second')) {
                $payload['duration'] = $request->pe_hours . ':' . $request->pe_minute . ':' . $request->pe_second;
                $payload['sets'] = null;
                $payload['based'] = null;
                $payload['seconds_per_rep'] = null;
            } else {
                $payload['duration'] = $request->input('pe_duration', $packageExercise->duration);
            }
        }

        if ($type === 'sets') {
            $payload['based'] = $request->input('pe_based', $packageExercise->based);
            $payload['seconds_per_rep'] = $request->input('pe_seconds_per_rep', $packageExercise->seconds_per_rep);
            $payload['duration'] = null;
            $payload['sets'] = $this->buildSetsFromRequest($request) ?? $packageExercise->sets;
        }

        $packageExercise->update($payload);
    }

    protected function buildSetsFromRequest(PackageRequest $request): ?array
    {
            $reps = $request->input('pe_reps');

        if ($reps === null || !is_array($reps)) {
            return null;
        }

        // Drop empty trailing rows; require at least one non-empty reps value
        $filtered = [];
        foreach ($reps as $i => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $filtered[] = $i;
        }

        if (empty($filtered)) {
            return null;
        }

        $weights = $request->input('pe_weight', []);
        $rests = $request->input('pe_rest', []);
        $times = $request->input('pe_time', []);

        $sets = [];
        foreach ($filtered as $i) {
            $sets[] = [
                'reps'   => $reps[$i],
                'weight' => isset($weights[$i]) && $weights[$i] !== null && $weights[$i] !== '' ? $weights[$i] : null,
                'rest'   => $rests[$i] ?? null,
                'time'   => $times[$i] ?? null,
            ];
        }

        return $sets;
    }
        protected function updatePackageExerciseFromData(PackageExercise $packageExercise, array $data): void
    {
        $type = $data['pe_type'] ?? $packageExercise->type;
        
        $payload = [
            'title'           => $data['pe_title'] ?? $packageExercise->title,
            'instruction'     => $data['pe_instruction'] ?? $packageExercise->instruction,
            'tips'            => $data['pe_tips'] ?? $packageExercise->tips,
            'video_type'      => $data['pe_video_type'] ?? $packageExercise->video_type,
            'video_url'       => $data['pe_video_url'] ?? $packageExercise->video_url,
            'equipment_id'    => $data['pe_equipment_id'] ?? $packageExercise->equipment_id,
            'level_id'        => $data['pe_level_id'] ?? $packageExercise->level_id,
            'status'          => $data['pe_status'] ?? $packageExercise->status,
            'is_premium'      => isset($data['pe_is_premium']) ? (int) $data['pe_is_premium'] : $packageExercise->is_premium,
            'type'            => $type,
            'bodypart_ids'    => $data['pe_bodypart_ids'] ?? $packageExercise->bodypart_ids,
        ];

        if ($type === 'duration') {
            if (!empty($data['pe_hours']) && !empty($data['pe_minute']) && !empty($data['pe_second'])) {
                $payload['duration'] = $data['pe_hours'] . ':' . $data['pe_minute'] . ':' . $data['pe_second'];
                $payload['sets'] = null;
                $payload['based'] = null;
                $payload['seconds_per_rep'] = null;
            } else {
                $payload['duration'] = $data['pe_duration'] ?? $packageExercise->duration;
            }
        }

        if ($type === 'sets') {
            $payload['based'] = $data['pe_based'] ?? $packageExercise->based;
            $payload['seconds_per_rep'] = $data['pe_seconds_per_rep'] ?? $packageExercise->seconds_per_rep;
            $payload['duration'] = null;
            $payload['sets'] = $this->buildSetsFromArray($data) ?? $packageExercise->sets;
        }

        $packageExercise->update($payload);
    }

    protected function buildSetsFromArray(array $data): ?array
    {
        $reps = $data['pe_reps'] ?? null;
        if ($reps === null || !is_array($reps)) {
            return null;
        }

        $filtered = [];
        foreach ($reps as $i => $value) {
            if ($value !== null && $value !== '') {
                $filtered[] = $i;
            }
        }

        if (empty($filtered)) {
            return null;
        }

        $weights = $data['pe_weight'] ?? [];
        $rests = $data['pe_rest'] ?? [];
        $times = $data['pe_time'] ?? [];

        $sets = [];
        foreach ($filtered as $i) {
            $sets[] = [
                'reps'   => $reps[$i],
                'weight' => $weights[$i] ?? null,
                'rest'   => $rests[$i] ?? null,
                'time'   => $times[$i] ?? null,
            ];
        }
        return $sets;
    }

    public function getPackageFullData($id)
    {
        $package = Package::with('packageExercises')->findOrFail($id);
        
        // Format the exercises exactly how the JS expects them
        $exercises = $package->packageExercises->map(function($pe) {
            $duration = $pe->duration ? explode(':', $pe->duration) : [null, null, null];
            return [
                'exercise_id' => $pe->exercise_id,
                'pe_title' => $pe->title,
                'pe_instruction' => $pe->instruction,
                'pe_tips' => $pe->tips,
                'pe_video_type' => $pe->video_type,
                'pe_video_url' => $pe->video_url,
                'pe_status' => $pe->status,
                'pe_is_premium' => $pe->is_premium,
                'pe_type' => $pe->type,
                'pe_based' => $pe->based,
                'pe_seconds_per_rep' => $pe->seconds_per_rep,
                'pe_hours' => $duration[0],
                'pe_minute' => $duration[1],
                'pe_second' => $duration[2],
                'pe_reps' => collect($pe->sets ?? [])->pluck('reps')->toArray(),
                'pe_time' => collect($pe->sets ?? [])->pluck('time')->toArray(),
                'pe_weight' => collect($pe->sets ?? [])->pluck('weight')->toArray(),
                'pe_rest' => collect($pe->sets ?? [])->pluck('rest')->toArray(),
            ];
        });

        return response()->json([
            'package' => $package->only(['name', 'duration_unit', 'duration', 'price', 'description', 'status', 'diet_id', 'advice_id', 'follow_up_price', 'food_recognition_limit']),
            'exercises' => $exercises
        ]);
    }
}




