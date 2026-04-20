<?php

namespace App\Http\Requests\Request;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RequestRequest extends FormRequest
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
			'user_id' => ['required','integer'],
			'company' => ['nullable','string','max:40'],
			'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
			'space_type' => ['nullable', 'string', Rule::in(['billboard', 'digital'])],
			'project_type' => ['nullable', 'string', Rule::in(['standard_print', 'special_project', 'video_quote', 'advisory'])],
			'description' => ['nullable','string', "max:500"],
			'budget_description' => ['nullable','string', "max:200"],
			'tentative_start_date' => ['nullable', Rule::date()->format('Y-m-d')]
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
