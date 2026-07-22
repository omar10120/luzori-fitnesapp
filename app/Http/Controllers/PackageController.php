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

        return view('package.form', compact('pageTitle', 'diets', 'advices', 'exercises', 'users'));
    }

    public function store(PackageRequest $request)
    {
        if( !auth()->user()->can('package-add') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        DB::transaction(function () use ($request) {
            $package = Package::create($request->only([
                'name', 'duration_unit', 'duration', 'price', 'description', 'status',
                'diet_id', 'advice_id', 'exercise_id', 'follow_up_price', 'food_recognition_limit',
            ]));

            storeMediaFile($package, $request->package_image, 'package_image');

            $package->users()->attach($request->users ?? []);

            $exercise = Exercise::findOrFail($request->exercise_id);
            $packageExercise = $this->cloneExerciseToPackage($package, $exercise);
            $this->updatePackageExerciseFromRequest($packageExercise, $request);
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
        if( !auth()->user()->can('package-edit') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = Package::with('packageExercise')->findOrFail($id);
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.package') ]);
        $diets = Diet::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $advices = Advice::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $exercises = Exercise::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $users = User::where('status', 'active')->orderBy('username')->pluck('username', 'id');
        $packageExercise = $data->packageExercise;

        return view('package.form', compact('data','id','pageTitle', 'diets', 'advices', 'exercises', 'users', 'packageExercise'));
    }

    public function update(PackageRequest $request, $id)
    {
        if( !auth()->user()->can('package-edit') ) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $package = Package::with('packageExercise')->findOrFail($id);

        DB::transaction(function () use ($request, $package) {
            $previousExerciseId = $package->exercise_id;

            $package->fill($request->only([
                'name', 'duration_unit', 'duration', 'price', 'description', 'status',
                'diet_id', 'advice_id', 'exercise_id', 'follow_up_price', 'food_recognition_limit',
            ]))->update();

            $package->users()->sync($request->users ?? []);

            if (isset($request->package_image) && $request->package_image != null) {
                $package->clearMediaCollection('package_image');
                $package->addMediaFromRequest('package_image')->toMediaCollection('package_image');
            }

            $exerciseChanged = (int) $previousExerciseId !== (int) $request->exercise_id;
            $missingClone = !$package->packageExercise;

            if ($exerciseChanged || $missingClone) {
                $exercise = Exercise::findOrFail($request->exercise_id);
                $packageExercise = $this->cloneExerciseToPackage($package, $exercise);
                $this->updatePackageExerciseFromRequest($packageExercise, $request);
            } else {
                $this->updatePackageExerciseFromRequest($package->packageExercise, $request);
            }
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
}
