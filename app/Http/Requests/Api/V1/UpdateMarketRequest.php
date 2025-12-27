<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('markets.edit');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'nullable', 'exists:market_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('markets', 'slug')->ignore($this->route('market'))],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'cover_photo' => ['nullable', 'image', 'max:4096'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'operating_hours' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->name && !$this->slug) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}
