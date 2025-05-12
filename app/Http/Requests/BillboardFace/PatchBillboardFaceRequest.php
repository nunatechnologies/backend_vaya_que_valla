<?php

namespace App\Http\Requests\BillboardFace;

use App\Enums\BillboardFaceStatus;
use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PatchBillboardFaceRequest extends FormRequest
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
			'code' => ['sometimes','string','max:10','unique:billboard_faces,code,'.$this->route("id")],
			'billboard_id' => ['sometimes','integer','exists:billboards,id'],
			'face' => ['sometimes','string','max:10'],
			'location_detail' => ['sometimes','string','max:255'],
            'status'=>['required', new Enum(BillboardFaceStatus::class)],
            'rented_from' => ['sometimes','nullable',Rule::date()->format('Y-m-d')],
            'available_from' => ['sometimes','nullable',Rule::date()->format('Y-m-d')],
            'image' => ['nullable','image','max:2048']
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
