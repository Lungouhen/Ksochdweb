<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
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
        $postId = $this->route('post')->id ?? null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('posts', 'slug')->ignore($postId), 'max:255'],
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
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'body.required' => 'Post content cannot be empty.',
            'slug.unique' => 'This slug is already in use by another post.',
            'status.in' => 'Please select a valid status.',
        ];
    }
}
