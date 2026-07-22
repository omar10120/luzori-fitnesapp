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
        $rules = [
            'name'                     => 'required|string|max:255',
            'duration_unit'            => 'required|in:monthly,yearly',
            'duration'                 => 'required|integer|min:1|max:12',
            'price'                    => 'required|numeric|min:0',
            'description'              => 'nullable|string',
            'status'                   => 'required|in:active,inactive',
            'diet_id'                  => 'required|exists:diets,id',
            'advice_id'                => 'required|exists:advice,id',
            'exercise_id'              => 'required|exists:exercises,id',
            'follow_up_price'          => 'required|numeric|min:0',
            'food_recognition_limit'   => 'required|integer|min:0',
            'users'                    => 'nullable|array',
            'users.*'                  => 'exists:users,id',
            'package_image'            => 'nullable|image',
            'pe_title'                 => 'nullable|string|max:255',
            'pe_instruction'           => 'nullable|string',
            'pe_tips'                  => 'nullable|string',
            'pe_video_type'            => 'nullable|string|in:url,upload_video',
            'pe_video_url'             => 'nullable|string',
            'pe_duration'              => 'nullable|string',
            'pe_based'                 => 'nullable|string|in:reps,time',
            'pe_type'                  => 'nullable|string|in:sets,duration',
            'pe_equipment_id'          => 'nullable|exists:equipment,id',
            'pe_level_id'              => 'nullable|exists:levels,id',
            'pe_status'                => 'nullable|in:active,inactive',
            'pe_is_premium'            => 'nullable|boolean',
            'pe_seconds_per_rep'       => 'nullable|integer|min:0',
            'pe_bodypart_ids'          => 'nullable|array',
            'pe_bodypart_ids.*'        => 'exists:body_parts,id',
            'pe_reps'                  => 'nullable|array',
            'pe_reps.*'                => 'nullable|numeric|min:0',
            'pe_time'                  => 'nullable|array',
            'pe_time.*'                => 'nullable|numeric|min:0',
            'pe_weight'                => 'nullable|array',
            'pe_weight.*'              => 'nullable|numeric|min:0',
            'pe_rest'                  => 'nullable|array',
            'pe_rest.*'                => 'nullable|numeric|min:0',
            'pe_hours'                 => 'nullable',
            'pe_minute'                => 'nullable',
            'pe_second'                => 'nullable',
        ];

        return $rules;
    }

    public function messages()
    {
        return [];
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
