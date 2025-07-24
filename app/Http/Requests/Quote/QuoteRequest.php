<?php

namespace App\Http\Requests\Quote;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use App\Models\BillboardFace;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
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
			'billboard_face_id' => ['required','integer','exists:billboard_faces,id'],
			'status' => ['required',Rule::in(['pending','approved','rejected'])],
			'start_date' => ['required', Rule::date()->format('Y-m-d')],
			'total_amount' => ['required','numeric','between:0,999999.99'],
			'months' => ['required','integer'],
            'digital_billboard_plan_id' => ['sometimes','integer','exists:digital_billboard_plans,id']
		];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $billboardFace = BillboardFace::find($this->input('billboard_face_id'));
            $billboardStructure = $billboardFace->billboard->billboardStructure->name;

            // if (is_null($billboardFace)) 
            // {
            //     $validator->errors()->add('billboard_face_id', 'The billboard_face is required for DIGITAL billboards.');
            // }
            if ($billboardStructure == 'DIGITAL' && !$this->filled('digital_billboard_plan_id')) 
            {
                $validator->errors()->add('digital_billboard_plan_id', 'El plan de valla digital es requerido cuando la valla es de tipo digital.');
            }
            elseif ($billboardStructure != 'DIGITAL' && $this->filled('digital_billboard_plan_id')) 
            {
                $validator->errors()->add('digital_billboard_plan_id', 'El plan de valla digital es requerida solo para las vallas digitales.');
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
