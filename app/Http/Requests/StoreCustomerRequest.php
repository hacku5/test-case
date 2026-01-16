<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur',
            'email.required' => 'E-posta alanı zorunludur',
            'email.email' => 'Geçerli bir e-posta adresi giriniz',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor',
        ];
    }
}
