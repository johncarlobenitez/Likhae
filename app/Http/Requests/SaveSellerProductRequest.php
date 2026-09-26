<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSellerProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAccountType('SELLER') === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => strtoupper((string) $this->input('status', $this->input('listing_status', 'DRAFT'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['DRAFT', 'ACTIVE', 'ARCHIVED'])],

            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'sku' => ['nullable', 'string', 'max:100'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'options' => ['nullable', 'array', 'max:5'],
            'options.*.name' => ['nullable', 'string', 'max:100'],
            'options.*.values' => ['nullable', 'string', 'max:1000'],

            'variants' => ['nullable', 'array', 'max:100'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', 'distinct'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'variants.*.values' => ['nullable', 'string', 'max:500'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ];
    }
}
