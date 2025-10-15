@extends('layouts.app')
        @section('content')
<div class="container">
    <h1>Корзина</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
        <form action="{{ route('checkout') }}" method="POST" >
            @csrf
            <button type="submit" class="btn btn-success">Оформить заказ</button>
        </form>
    @endif
</div>
@endsection 