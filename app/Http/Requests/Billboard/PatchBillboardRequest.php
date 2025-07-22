<?php

namespace App\Http\Requests\Billboard;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatchBillboardRequest extends FormRequest
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
			'name' => ['sometimes','string','max:255'],
			'location' => ['sometimes','string','max:255'],
			'advertiser_id' => ['sometimes','integer'],
			// 'status' => ['sometimes'],
			'city_id' => ['sometimes','integer'],
            'zone_id' => ['sometimes','integer'],
			'billboard_structure_id' => ['sometimes','integer'],
			// 'entity_status' => ['sometimes'],
			'size' => ['sometimes','string','max:255'],
			'price_per_month' => ['sometimes','numeric','between:0,99999999.99'],
			// 'traffic_data' => ['sometimes'],
			'longitude' => ['sometimes','numeric'],
			'latitude' => ['sometimes','numeric']
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
