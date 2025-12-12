<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class OrderRequest extends FormRequest
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
        $order = $this->route('order'); // имя параметра в роуте: {order}

        if($this->isMethod('post')) {
            return $user->can('create', Order::class);
        }
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return $order && $user->can('update', $order);
        }
        if($this->isMethod('delete')) {
            return $order && $user->can('delete', $order);
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
            //
        ];
    }
}
