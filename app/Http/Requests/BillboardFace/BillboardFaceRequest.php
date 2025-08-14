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

class BillboardFaceRequest extends FormRequest
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
			'code' => ['required','string','max:10','unique:billboard_faces,code'],
			// 'billboard_id' => ['required','integer','exists:billboards,id'],
			'face' => ['required','string','max:10'],
			'location_detail' => ['required','string','max:255'],
            'status'=>['required', new Enum(BillboardFaceStatus::class)],
            'rented_from' => ['sometimes',Rule::date()->format('Y-m-d')],
            'available_from' => ['sometimes',Rule::date()->format('Y-m-d')],
            'image' => ['nullable', 'image', 'max:2048'],
            'name' => ['required','string','max:255'],
			'location' => ['required','string','max:255'],
			'advertiser_id' => ['required','integer'],
			// 'status' => ['required'],
			'city_id' => ['required','integer'],
            'zone_id' => ['required','integer'],
			'billboard_structure_id' => ['required','integer'],
			// 'entity_status' => ['required'],
			'size' => ['required','string','max:255'],
			'price_per_month' => ['required','numeric','between:0,99999999.99'],
			// 'traffic_data' => ['required'],
			'longitude' => ['required','numeric'],
			'latitude' => ['required','numeric']
		];
    }

    public function attributes(): array
    {
        return [
            'code' => 'código',
            'face' => 'cara',
            'location_detail' => 'detalle de ubicación',
            'status' => 'estado',
            'rented_from' => 'rentado desde',
            'available_from' => 'disponible desde',
            'image' => 'imagen',
            'name' => 'nombre',
            'location' => 'ubicación',
            'advertiser_id' => 'proveedor',
            'city_id' => 'ciudad',
            'zone_id' => 'zona',
            'billboard_structure_id' => 'estructura',
            'size' => 'tamaño',
            'price_per_month' => 'precio por mes',
            'longitude' => 'longitud',
            'latitude' => 'latitud',
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
