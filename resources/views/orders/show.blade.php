@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
<div class="container">

    <h3>
    Заказ #{{ $order->id }}
    <span class="badge bg-{{ \App\Models\Order::STATUS_COLORS[$order->status] }}">
        {{ \App\Models\Order::STATUSES[$order->status] }}
    </span>
</h3>

<p class="text-muted">
    Оформлен: {{ $order->created_at->format('d.m.Y H:i') }}
</p>
<div class="card mb-4">
    <div class="card-header">Данные получателя</div>
    <div class="card-body">
        <p><strong>Имя:</strong> {{ $order->recipient_name }}</p>
        <p><strong>Телефон:</strong> {{ $order->phone }}</p>
        <p><strong>Адрес:</strong> {{ $order->address }}</p>
        @if($order->comment)
            <p><strong>Комментарий:</strong> {{ $order->comment }}</p>
        @endif
    </div>
</div>
<table class="table">
    <thead>
        <tr>
            <th>Товар</th>
            <th>Цена</th>
            <th>Кол-во</th>
            <th>Сумма</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td class="d-flex align-items-center">
                    <img src="{{ asset('storage/'.$item->goods->image) }}"
                         width="50" class="me-2 rounded">
                    {{ $item->goods->name }}
                </td>
                <td>{{ number_format($item->price, 2) }} UAH</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price * $item->quantity, 2) }} UAH</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h5 class="text-end">
    Итого: <strong>{{ number_format($order->total, 2) }} UAH</strong>
</h5>

        <div class="card-header">История изменений</div>
        <ul class="list-group list-group-flush">
            @foreach($order->statusLogs as $log)
                <li class="list-group-item">
                    <strong>{{ $log->user->name }}</strong>
                    изменил статус
                    <span class="badge bg-secondary">
                        {{ \App\Models\Order::STATUSES[$log->old_status] }}
                    </span>
                    →
                    <span class="badge bg-success">
                        {{ \App\Models\Order::STATUSES[$log->new_status] }}
                    </span>
                    <div class="text-muted small">
                        {{ $log->created_at->format('d.m.Y H:i') }}
                    </div>
                </li>
            @endforeach
        </ul>
    {{-- Кнопки --}}
    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            ← Назад
        </a>
    </div>
</div>
@endsection
