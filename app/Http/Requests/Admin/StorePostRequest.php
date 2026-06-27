<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePostRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'unique:posts,slug', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'template_layout' => ['required', 'in:classic-grid,editorial-news,donation-campaign,event-hub,minimalist-legal'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug from title if not provided
        if (empty($this->input('slug'))) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }
        
        // Auto-set published_at when status is published
        if ($this->input('status') === 'published' && !$this->input('published_at')) {
            $this->merge(['published_at' => now()]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'body.required' => 'Post content cannot be empty.',
            'status.in' => 'Please select a valid status.',
            'featured_image.image' => 'The featured image must be a valid image file.',
            'featured_image.max' => 'The featured image cannot exceed 2MB.',
        ];
    }
}
