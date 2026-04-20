<?php

namespace App\Http\Requests\DigitalBillboardPlan;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatchDigitalBillboardPlanRequest extends FormRequest
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
			'passes_per_hour' => ['sometimes','integer'],
			'price_per_month' => ['sometimes','numeric','min:0'],
			'seconds_per_day' => ['sometimes','integer','min:0'],
			'max_videos' => ['sometimes','integer','min:1'],
			'description' => ['sometimes']
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
