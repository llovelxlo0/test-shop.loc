@extends('layouts.app')
@section('title', 'Уведомления')
@section('content')
<div class="container">
    <h2 class="mb-4">Уведомления</h2>
     <form method="POST" action="{{ route('notifications.readAll') }}">
    @csrf
    <button class="btn btn-sm btn-outline-secondary">
        Отметить как прочитанные
    </button>
</form>
@foreach($notifications as $notification)
    @php
        $type = $notification->data['type'] ?? null;
    @endphp
   

    <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
        <div>
            @if($type === 'order_status_changed')
                Заказ #{{ $notification->data['order_id'] ?? '?' }}:
                статус изменён с
                <span class="badge bg-secondary">
                    {{ \App\Models\Order::STATUSES[$notification->data['old_status'] ?? ''] ?? $notification->data['old_status'] }}
                </span>
                на
                <span class="badge bg-success">
                    {{ \App\Models\Order::STATUSES[$notification->data['new_status'] ?? ''] ?? $notification->data['new_status'] }}
                </span>
            @else
                Новое уведомление
            @endif

            <div class="text-muted small">
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>

        @if(is_null($notification->read_at))
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <button class="btn btn-sm btn-outline-success">Прочитано</button>
            </form>
        @endif
        
    </div>
@endforeach
@if($notifications->isEmpty())
    <p class="text-muted">У вас нет уведомлений.</p>
@endif
<div class="mt-4">
        <a href="{{ route('profile') }}" class="btn btn-secondary">
            ← Назад
        </a>
    </div>
</div>
@endsection