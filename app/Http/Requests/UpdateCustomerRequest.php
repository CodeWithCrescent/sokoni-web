<?php
/**
 * Command to create this request:
 * php artisan make:request UpdateCustomerRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:100',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($this->customer->id),
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|required|string|min:8',
            'address' => 'sometimes|required|string',
        ];
    }
}