<?php

namespace App\Http\Requests\Billboard;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BillboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'name' => ['required','string','max:255'],
			'location' => ['required','string','max:255'],
			'advertiser_id' => ['required','integer'],
			'status' => ['required'],
			'billboard_type_id' => ['required','integer'],
			'city_id' => ['required','integer'],
			'billboard_structure_id' => ['required','integer'],
			'entity_status' => ['required'],
			'size' => ['required','string','max:255'],
			'price_per_month' => ['required','numeric','between:0,99999999.99'],
			'traffic_data' => ['required'],
			'image' => ['required','string','max:255'],
			'longitude' => ['required','numeric','between:0,999.9999999'],
			'latitude' => ['required','numeric','between:0,999.9999999']
		];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error(
                ErrorMessages::UNPROCESSABLE_ENTITY,
                $validator->errors(),
                [],
                422
            )
        );
    }
}
