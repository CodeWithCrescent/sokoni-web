<?php
/**
 * Command to create this request:
 * php artisan make:request UpdateOrderRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Should be handled by middleware or policy
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
            'delivery_id' => 'sometimes|nullable|exists:delivery_personnel,id',
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['pending', 'processing', 'delivered', 'cancelled']),
            ],
        ];
    }
}