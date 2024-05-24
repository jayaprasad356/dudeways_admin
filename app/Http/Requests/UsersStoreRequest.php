<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersStoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:20',
            'age' => 'required|string|max:100',
            'email' => 'nullable|email',
            'mobile' => 'nullable|string|max:10',
            'gender' => 'nullable|string',
            'avatar' => 'nullable|profile',
            'refer_code' => 'nullable|string',
            'referred_by' => 'nullable|string',
            'profession' => 'nullable|string',
            'points' => 'required|integer',
            'datetime' => 'nullable|datetime',
        ];
    }
}
