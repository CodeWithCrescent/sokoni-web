<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('market-products.edit');
    }

    public function rules(): array
    {
        return [
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
            'bulk_prices' => ['nullable', 'array'],
            'bulk_prices.*.min_qty' => ['required_with:bulk_prices', 'integer', 'min:1'],
            'bulk_prices.*.max_qty' => ['nullable', 'integer', 'min:1'],
            'bulk_prices.*.price' => ['required_with:bulk_prices', 'numeric', 'min:0'],
        ];
    }
}
