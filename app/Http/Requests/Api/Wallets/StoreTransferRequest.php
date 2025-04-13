<?php

namespace App\Http\Requests\Api\Wallets;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
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
            'source_user_id' => ['required', 'integer'],
            'destination_user_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
