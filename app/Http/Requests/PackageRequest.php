<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Main Package Data
            'name'                     => 'required|string|max:255',
            'duration_unit'            => 'required|in:monthly,yearly',
            'duration'                 => 'required|integer|min:1|max:12',
            'price'                    => 'required|numeric|min:0',
            'description'              => 'nullable|string',
            'status'                   => 'required|in:active,inactive',
            'diet_id'                  => 'required|exists:diets,id',
            'advice_id'                => 'required|exists:advice,id',
            'follow_up_price'          => 'required|numeric|min:0',
            'food_recognition_limit'   => 'required|integer|min:0',
            'users'                    => 'nullable|array',
            'users.*'                  => 'exists:users,id',
            'package_image'            => 'nullable|image',

            // Multiple Exercises Data
            'exercises'                => 'required|array|min:1',
            'exercises.*.exercise_id'  => 'required|exists:exercises,id|distinct', 

            
            // Exercise Overrides
            'exercises.*.pe_title'             => 'nullable|string|max:255',
            'exercises.*.pe_instruction'       => 'nullable|string',
            'exercises.*.pe_tips'              => 'nullable|string',
            'exercises.*.pe_video_type'        => 'nullable|string|in:url,upload_video',
            'exercises.*.pe_video_url'         => 'nullable|string',
            'exercises.*.pe_duration'          => 'nullable|string',
            'exercises.*.pe_based'             => 'nullable|string|in:reps,time',
            'exercises.*.pe_type'              => 'nullable|string|in:sets,duration',
            'exercises.*.pe_equipment_id'      => 'nullable|exists:equipment,id',
            'exercises.*.pe_level_id'          => 'nullable|exists:levels,id',
            'exercises.*.pe_status'            => 'nullable|in:active,inactive',
            'exercises.*.pe_is_premium'        => 'nullable|boolean',
            'exercises.*.pe_seconds_per_rep'   => 'nullable|integer|min:0',
            'exercises.*.pe_bodypart_ids'      => 'nullable|array',
            'exercises.*.pe_bodypart_ids.*'    => 'exists:body_parts,id',
            
            // Sets Data
            'exercises.*.pe_reps'              => 'nullable|array',
            'exercises.*.pe_reps.*'            => 'nullable|numeric|min:0',
            'exercises.*.pe_time'              => 'nullable|array',
            'exercises.*.pe_time.*'            => 'nullable|numeric|min:0',
            'exercises.*.pe_weight'            => 'nullable|array',
            'exercises.*.pe_weight.*'          => 'nullable|numeric|min:0',
            'exercises.*.pe_rest'              => 'nullable|array',
            'exercises.*.pe_rest.*'            => 'nullable|numeric|min:0',
            'exercises.*.pe_hours'             => 'nullable',
            'exercises.*.pe_minute'            => 'nullable',
            'exercises.*.pe_second'            => 'nullable',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' =>  $validator->errors()
        ];

        if ( request()->is('api*')){
           throw new HttpResponseException( response()->json($data,422) );
        }

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}