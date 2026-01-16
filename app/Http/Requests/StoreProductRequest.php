<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:1'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ürün adı zorunludur',
            'price.required' => 'Fiyat zorunludur',
            'price.min' => 'Fiyat en az 1 olmalıdır',
            'stock_quantity.required' => 'Stok miktarı zorunludur',
            'stock_quantity.min' => 'Stok miktarı negatif olamaz',
        ];
    }
}
