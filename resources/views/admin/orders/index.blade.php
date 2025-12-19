@extends('layouts.app')

@section('title', 'Все заказы')

@section('content')
<div class="container">
    <h2 class="mb-4">Все заказы</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="user" class="form-control"
               placeholder="Пользователь (ID или имя)"
               value="{{ request('user') }}">
    </div>

    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Все статусы</option>
            @foreach(\App\Models\Order::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" name="from" class="form-control"
               value="{{ request('from') }}">
    </div>

    <div class="col-md-2">
        <input type="date" name="to" class="form-control"
               value="{{ request('to') }}">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Фильтр</button>
    </div>
</form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Контакты</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th width="220">Управление</th>
                    <th>Подробнее</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>

                        <td>
                            @if($order->user)
                                {{ $order->user->name }}<br>
                                <small class="text-muted">ID: {{ $order->user->id }}</small>
                            @else
                                <span class="text-muted">Гость</span>
                            @endif
                        </td>

                        <td>
                            {{ $order->recipient_name }}<br>
                            {{ $order->phone }}<br>
                            <small class="text-muted">{{ $order->address }}</small>
                        </td>

                        <td>
                            {{ $order->created_at->format('d.m.Y H:i') }}
                        </td>

                        <td>
                            <strong>
                                {{ number_format($order->total, 2) }}
                                {{ $order->currency }}
                            </strong>
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
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                                @csrf
                                @method('PATCH')

                                <select name="status" class="form-select form-select-sm">
                                    @foreach(\App\Models\Order::STATUSES as $value => $label)
                                        <option value="{{ $value }}" @selected($order->status === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="btn btn-sm btn-success mt-1">✔</button>
                            </form>
                            <td class="text-center">
                                @can('view', $order)
                                    <a href="{{ route('profile.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Подробнее
                                    </a>
                                @endcan
                            </td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
    </div>
    <div class="mt-4">
        <a href="{{ route('profile') }}" class="btn btn-secondary">
            ← Назад
        </a>
    </div>
</div>
@endsection
