<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripsStoreRequest extends FormRequest
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
            'planning' => 'nullable|string',
            'location' => 'nullable|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'name_of_your_trip' => 'nullable|string',
            'description_of_your_trip' => 'nullable|string',
        ];
    }
}
