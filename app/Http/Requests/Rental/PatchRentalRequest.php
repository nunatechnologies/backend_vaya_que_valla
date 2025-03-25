<?php

namespace App\Http\Requests\Rental;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatchRentalRequest extends FormRequest
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
			'user_id' => ['sometimes','integer'],
			'quote_id' => ['sometimes','integer'],
			'starts_at' => ['sometimes'],
			'ends_at' => ['sometimes'],
			'status' => ['sometimes'],
			'has_lona' => ['sometimes'],
			'total_amount' => ['sometimes','numeric','between:0,999999.99'],
			'unsubscribe_status' => ['sometimes','string','max:150']
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
