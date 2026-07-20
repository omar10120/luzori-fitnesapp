<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscriptionRequest  extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = strtolower($this->method());

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'user_id' => 'required',
                    'package_id' => 'required',
                    'is_follow_up' => 'required',
                ];
                break;
            case 'patch':
                $rules = [
                    'user_id' => 'required',
                    'package_id' => 'required',
                    'is_follow_up' => 'required',
                ];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'user_id.required' => __('message.request_required',['name'=>__('message.user')]),
            'package_id.required' => __('message.request_required',['name'=>__('message.package')]),
            'is_follow_up.required' => __('message.request_required',['name'=>__('message.is_follow_up')]),
            'is_follow_up.in' => __('message.request_in',['name'=>__('message.is_follow_up')]),
            'is_follow_up.boolean' => __('message.request_boolean',['name'=>__('message.is_follow_up')]),
         ];
    }
    /**
     * @param Validator $validator
     */
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
            throw new HttpResponseException(response()->json([ 'status' => false, 'event' => 'validation', 'message' => $validator->errors()->first() ]));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
