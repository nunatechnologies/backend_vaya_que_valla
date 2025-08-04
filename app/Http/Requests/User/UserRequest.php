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

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cod_phone' => 'nullable|string',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'rol'=>['required', new Enum(RolSpatie::class)],
            'user_type' => ['required', 'string', Rule::in([
                UserType::ORGANIZATION->name,
                UserType::PERSON->name
            ])],
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
