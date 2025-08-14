<?php

namespace App\Http\Requests\User;

use App\Enums\GenderType;
use App\Enums\RolSpatie;
use App\Enums\UserType;
use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PatchUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'cod_phone' => 'nullable|string',
            'phone' => 'nullable|string|unique:users,phone,'. $this->route('id'),
            'rol'=>['sometimes', new Enum(RolSpatie::class)],
            'user_type' => ['sometimes', 'string', Rule::in([
                UserType::ORGANIZATION->name,
                UserType::PERSON->name
            ])],
            'entity_status' => ['sometimes', 'string', Rule::in([
                'active',
                'inactive'
            ])],
            'ci' => ['sometimes','required_if:user_type,PERSON', 'string','max:10'],
            'social_reason' => ['sometimes','required_if:user_type,ORGANIZATION', 'string','max:50'],
            'name_contact' => ['sometimes','required_if:user_type,ORGANIZATION', 'string','max:30'],
            'phone_contact' => ['sometimes','required_if:user_type,ORGANIZATION', 'string','max:15'],
            'commision_percentage' => ['sometimes','required_if:user_type,ORGANIZATION', 'numeric','min:0','max:100'],
            'nit' => ['sometimes','required_if:user_type,ORGANIZATION', 'string','min:1','max:20'],
            'category_id' => ['sometimes','required_if:user_type,ORGANIZATION', 'exists:categories,id']

        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'correo',
            'cod_phone' => 'codigo de teléfono',
            'phone' => 'teléfono',
            'rol' => 'rol',
            'user_type' => 'tipo de usuario',
            'ci' => 'C.I.',
            'social_reason' => 'razón social',
            'name_contact' => 'nombre de contacto',
            'phone_contact' => 'teléfono de contacto',
            'commision_percentage' => 'porcentaje de comisión',
            'nit' => 'NIT',
            'category_id' => 'categoria',
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
