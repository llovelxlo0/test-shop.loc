<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Review;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

    if (!$user) {
        return false;
    }

    /** @var \App\Models\Review|null $review */
    $review = $this->route('review'); // {review} в роуте

    if ($this->isMethod('post')) {
        return $user->can('create', Review::class);
    }

    if ($this->isMethod('put') || $this->isMethod('patch')) {
        return $review && $user->can('update', $review);
    }

    if ($this->isMethod('delete')) {
        return $review && $user->can('delete', $review);
    }

    return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ];
    }
}
