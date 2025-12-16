@extends('layouts.app')
        @section('content')
<div class="container">
    <h1>Корзина</h1>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @if($cartItems->isEmpty())
        <p>Ваша корзина пуста.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Итого</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    <tr>
                        <td>{{ $item['goods']->name }}</td> <!-- название товара -->
                        <td>{{ number_format($item['price'], 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="goods_id" value="{{ $item['goods']->id }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control d-inline w-auto">
                                <button type="submit" class="btn btn-primary btn-sm">Обновить</button>
                            </form>
                        </td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="goods_id" value="{{ $item['goods']->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

            </tbody>
        </table>
        <h3>Общая сумма: {{ number_format($total, 2) }}</h3>
        <form action="{{ route('checkout') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Имя получателя</label>
            <input type="text"
                name="recipient_name"
                value="{{ old('recipient_name') }}"
                class="form-control @error('recipient_name') is-invalid @enderror"
                required>
            @error('recipient_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Телефон</label>
            <input type="text"
                name="phone"
                value="{{ old('phone') }}"
                class="form-control @error('phone') is-invalid @enderror"
                required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Адрес</label>
            <input type="text"
                name="address"
                value="{{ old('address') }}"
                class="form-control @error('address') is-invalid @enderror"
                required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Комментарий (необязательно)</label>
            <textarea name="comment"
                    class="form-control @error('comment') is-invalid @enderror"
                    rows="3">{{ old('comment') }}</textarea>
            @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Оформить заказ</button>
    </form>

    @endif
</div>
@endsection 