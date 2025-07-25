<?php

namespace App\Http\Requests\User;

use App\Enums\RolSpatie;
use App\Enums\UserType;
use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'cod_phone' => ['nullable', 'string', 'max:5'],
            'phone' => ['nullable', 'string', 'max:10','unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'user_type' => ['required', 'string', Rule::in([
                UserType::ORGANIZATION->name,
                UserType::PERSON->name
            ])],
            'role'=>['required', new Enum(RolSpatie::class)],
            // Not required right now, will be validated conditionally
            'ci' => ['nullable', 'string','max:10'],
            'social_reason' => ['nullable', 'string','max:50'],
            'name_contact' => ['nullable', 'string','max:30'],
            'phone_contact' => ['nullable', 'string','max:15'],
            'commision_percentage' => ['nullable', 'numeric','min:0','max:100'],
            'nit' => ['nullable', 'string','min:1','max:20'],
            'category_id' => ['nullable', 'exists:categories,id']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userType = $this->input('user_type');

            if ($userType === UserType::PERSON->name) {
                if (!$this->filled('ci')) {
                    $validator->errors()->add('ci', 'El CI es requerido cuando el tipo de usuario es PERSON.');
                }
            }

            if ($userType === UserType::ORGANIZATION->name) {
                if (!$this->filled('social_reason')) {
                    $validator->errors()->add('social_reason', 'La razon social para el tipo de usuario ORGANIZATION.');
                }
                if (!$this->filled('commision_percentage')) {
                    $validator->errors()->add('commision_percentage', 'El porcentage de comision es requerido para el tipo de usuario ORGANIZATION.');
                }
                if (!$this->filled('nit')) {
                    $validator->errors()->add('nit', 'El nit es requerido para el tipo de usuario ORGANIZATION.');
                }
                
                if (!$this->filled('category_id')) {
                    $validator->errors()->add('category_id', 'La categoria es requerida para el tipo de usuario ORGANIZATION.');
                }
            }
        });
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
