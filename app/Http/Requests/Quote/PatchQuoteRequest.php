<?php

namespace App\Http\Requests\Quote;

use App\Http\Messages\ErrorMessages;
use App\Http\Responses\ApiResponse;
use App\Models\BillboardFace;
use App\Models\Quote;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PatchQuoteRequest extends FormRequest
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
			'billboard_face_id' => ['sometimes','integer','exists:billboard_faces,id'],
			'status' => ['sometimes',Rule::in(['pending','approved','rejected'])],
			'start_date' => ['sometimes', Rule::date()->format('Y-m-d')],
			'total_amount' => ['sometimes','numeric','between:0,999999.99'],
			'months' => ['sometimes','integer'],
            'digital_billboard_plan_id' => ['sometimes','integer','exists:digital_billboard_plans,id']
		];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->filled('billboard_face_id')) {
                $billboardFace = BillboardFace::find($this->input('billboard_face_id'));
            }
            else{
                $billboardFace = Quote::find($this->id)->billboardFace;
            }
            
            $billboardStructure = $billboardFace->billboard->billboardStructure->name;

            if ($billboardStructure == 'DIGITAL' && !$this->filled('digital_billboard_plan_id')) 
            {
                $validator->errors()->add('digital_billboard_plan_id', 'The digital_billboard_plan_id is required for DIGITAL billboards.');
            }
            elseif ($billboardStructure != 'DIGITAL' && $this->filled('digital_billboard_plan_id')) 
            {
                $validator->errors()->add('digital_billboard_plan_id', 'The digital_billboard_plan_id is required ONLY FOR DIGITAL billboards.');
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
