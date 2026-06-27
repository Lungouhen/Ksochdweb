<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin') || $this->user()->hasRole('editor');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:pages,slug', 'max:255'],
            'body' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
            'template_layout' => ['required', 'in:minimalist-legal,classic-grid,donation-campaign,event-hub,editorial-news'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->input('slug'))) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A page title is required.',
            'body.required' => 'Page content cannot be empty.',
            'status.in' => 'Please select a valid status.',
        ];
    }
}
