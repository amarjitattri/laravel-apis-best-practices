<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $method = $this->method();

        if ($method == 'PUT'){
            return [
                'customerId' => ['required', 'integer'],
                'amount' => ['required', 'integer' ],
                'status' => ['required', Rule::in(['B', 'P', 'V'])],
                'billedDate' => ['required', 'date_format:Y-m-d'],
                'paidDate' => ['required', 'date_format:Y-m-d'],
            ];
        }else {
            return [
                'customerId' => ['sometimes', 'required', 'integer'],
                'amount' => ['sometimes', 'required', 'integer' ],
                'status' => ['sometimes', 'required', Rule::in(['B', 'P', 'V'])],
                'billedDate' => ['sometimes', 'required', 'date_format:Y-m-d'],
                'paidDate' => ['sometimes', 'required', 'date_format:Y-m-d'],
            ];
        }
    }
    protected function prepareForValidation() {
        $this->customerId ? $this->merge(['customer_id' => $this->customerId]) : NULL;
        $this->billedDate ? $this->merge(['bille_date' => $this->billedDate]) : NULL;
        $this->paidDate ? $this->merge(['paid_date' => $this->paidDate]) : NULL;
    }
}
