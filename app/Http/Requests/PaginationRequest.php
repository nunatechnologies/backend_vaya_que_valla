<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'rol' =>'nullable|string',
            'status' =>'nullable|string',
            'itemsPerPage' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
            'sortBy' => 'nullable|string|max:255',
            'orderBy' => 'nullable|in:asc,desc',
        ];
    }
}
