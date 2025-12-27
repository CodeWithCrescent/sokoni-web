<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMarketProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('market-products.create');
    }

    public function rules(): array
    {
        return [
            'market_id' => ['required', 'exists:markets,id'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('market_products')->where(function ($query) {
                    return $query->where('market_id', $this->market_id);
                }),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
            'bulk_prices' => ['nullable', 'array'],
            'bulk_prices.*.min_qty' => ['required_with:bulk_prices', 'integer', 'min:1'],
            'bulk_prices.*.max_qty' => ['nullable', 'integer', 'min:1'],
            'bulk_prices.*.price' => ['required_with:bulk_prices', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.unique' => 'This product is already added to the selected market.',
        ];
    }
}
