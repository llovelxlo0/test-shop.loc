@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Все заказы</h1>

    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Получатель</th>
            <th>Телефон</th>
            <th>Адрес</th>
            <th>Комментарий</th>
            <th>Итого</th>
            <th>Дата</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>
                    {{ $order->user->name ?? 'Гость' }}
                    @if($order->user_id)
                        <div class="text-muted small">ID: {{ $order->user_id }}</div>
                    @endif
                </td>
                <td>{{ $order->recipient_name }}</td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->address }}</td>
                <td style="max-width: 240px;">
                    <div class="text-truncate">{{ $order->comment }}</div>
                </td>
                <td><strong>{{ number_format($order->total, 2) }} {{ $order->currency ?? 'UAH' }}</strong></td>
                <td class="text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
    <div class="mt-4">
        <a href="{{ route('profile') }}" class="btn btn-secondary">← Назад в профиль</a>
    </div>
</div>
@endsection
