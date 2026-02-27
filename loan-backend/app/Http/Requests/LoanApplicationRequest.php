<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanApplicationRequest extends FormRequest
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
            'income' => 'required|numeric|min:0',
            'age' => 'nullable|integer|min:18|max:120',
            'employment' => 'nullable|string|max:100',
            'credit_history' => 'nullable|string|max:50',
            'requested_amount' => 'required|numeric|min:100',
            'purpose' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nome é obrigatório',
            'income.required' => 'Renda é obrigatória',
            'income.numeric' => 'Renda deve ser um número',
            'requested_amount.required' => 'Valor solicitado é obrigatório',
            'requested_amount.numeric' => 'Valor solicitado deve ser um número',
            'age.min' => 'Idade mínima é 18 anos',
            'email.email' => 'Email inválido',
        ];
    }
}
