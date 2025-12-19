@extends('layouts.app')

@section('title', 'История заказов')

@section('content')
<div class="container">
    <h2 class="mb-4">История заказов</h2>

    @if($orders->isEmpty())
        <div class="alert alert-info">
            У вас пока нет заказов.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Заказ</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>

                            <td>
                                {{ $order->created_at->format('d.m.Y') }}
                                <div class="text-muted small">
                                    {{ $order->created_at->format('H:i') }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-{{ match($order->status) {
                                    'pending' => 'warning',
                                    'paid' => 'success',
                                    'shipped' => 'primary',
                                    'completed' => 'secondary',
                                    'cancelled' => 'danger',
                                    default => 'light'
                                } }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ number_format($order->total, 2) }}
                                    {{ $order->currency }}
                                </strong>
                            </td>
                            <td>
                                @if($order->items->first()?->goods?->image)
                                    <img src="{{ asset('storage/'.$order->items->first()->goods->image) }}"
                                        width="40" class="rounded">
                                @endif
                            </td>

                            <td class="text-center">
                                @can('view', $order)
                                    <a href="{{ route('profile.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Подробнее
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
 <div class="mt-4">
        <a href="{{ route('profile') }}" class="btn btn-secondary">
            ← Назад
        </a>
    </div>
@endsection
