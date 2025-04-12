<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'phone' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
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
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userType = $this->input('user_type');

            if ($userType === UserType::PERSON->name) {
                if (!$this->filled('ci')) {
                    $validator->errors()->add('ci', 'The CI is required for user type PERSON.');
                }
            }

            if ($userType === UserType::ORGANIZATION->name) {
                if (!$this->filled('social_reason')) {
                    $validator->errors()->add('social_reason', 'The social_reason is required for user type ORGANIZATION.');
                }
                if (!$this->filled('name_contact')) {
                    $validator->errors()->add('name_contact', 'The contact_name is required for user type ORGANIZATION.');
                }
                if (!$this->filled('phone_contact')) {
                    $validator->errors()->add('phone_contact', 'The phone_contact is required for user type ORGANIZATION.');
                }
                if (!$this->filled('commision_percentage')) {
                    $validator->errors()->add('commision_percentage', 'The commision_percentage is required for user type ORGANIZATION.');
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
