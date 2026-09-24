<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->hasRole('buyer') && $this->user()?->email_verified_at);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'address_id' => ['required', Rule::exists('addresses', 'id')->where('user_id', $this->user()->id)],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'permit' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ];
    }
}
