<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSellerProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('seller') === true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where(fn ($query) => $query->whereNotNull('parent_id')->where('is_active', true))],
            'description' => ['required', 'string', 'max:3000'],
            'price' => ['required', 'decimal:0,2', 'min:0', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'weight_grams' => ['required', 'integer', 'min:1', 'max:1000000'],
            'listing_status' => ['required', Rule::in(['active', 'draft', 'archived'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array', 'max:7'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variation_id' => ['nullable', 'array'],
            'variation_id.*' => ['nullable', 'integer'],
            'variation_name' => ['nullable', 'array'],
            'variation_name.*' => ['nullable', 'string', 'max:80'],
            'variation_value' => ['nullable', 'array'],
            'variation_value.*' => ['nullable', 'string', 'max:160'],
            'variation_sku' => ['nullable', 'array'],
            'variation_sku.*' => ['nullable', 'string', 'max:80', 'distinct'],
            'variation_price' => ['nullable', 'array'],
            'variation_price.*' => ['nullable', 'decimal:0,2', 'min:0', 'max:999999.99'],
            'variation_stock' => ['nullable', 'array'],
            'variation_stock.*' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'variation_weight_grams' => ['nullable', 'array'],
            'variation_weight_grams.*' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'specifications_text' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
