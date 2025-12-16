<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Goods;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
class GoodsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

    // Если гость — сразу нет
    if (!$user) {
        return false;
    }

    $good = $this->route('good'); // имя параметра в роуте: {good}

    // Если создаём
    if ($this->isMethod('post')) {
        return $user->can('create', Goods::class);
    }

    if (in_array($this->method(), ['PUT','PATCH'], true)) {
        return $good && $user->can('update', $good);
    }

    if ($this->isMethod('delete')) {
        return $good && $user->can('delete', $good);
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
            // Базовые поля товара
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],

            // Для create можно сделать image обязательной, для update — опциональной.
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            // EAV: attributes[ключ]['name'], attributes[ключ]['value']
            'attributes'         => ['nullable', 'array'],
            'attributes.*.name'  => ['nullable', 'string', 'max:255'],
            'attributes.*.value' => ['nullable', 'string', 'max:255'],
        ];
    }
}
