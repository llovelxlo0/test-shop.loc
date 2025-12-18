@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">История покупок</h1>

    @if($orders->isEmpty())
        <p>У вас пока нет заказов.</p>
    @else
        @foreach($orders as $order)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>Заказ #{{ $order->id }}</strong>
                        <div class="text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div>
                        <strong>{{ number_format($order->total, 2) }} {{ $order->currency ?? 'UAH' }}</strong>
                    </div>
                </div>

                <hr class="my-2">

                @foreach($order->items as $item)
                    <div class="d-flex align-items-center mb-2">
                        @php $img = $item->goods->image ?? null; @endphp

                        @if($img)
                            <img src="{{ asset('storage/' . $img) }}"
                                 class="rounded me-2"
                                 style="width:50px;height:50px;object-fit:cover"
                                 alt="">
                        @else
                            <div class="rounded bg-light me-2" style="width:50px;height:50px;"></div>
                        @endif

                        <div>
                            <div>{{ $item->goods->name ?? 'Товар удалён' }}</div>
                            <div class="text-muted small">
                                {{ $item->quantity }} × {{ number_format($item->price, 2) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{ $orders->links() }}
    @endif
</div>
    <div class="mt-4">
        <a href="{{ route('profile') }}" class="btn btn-secondary">← Назад в профиль</a>
    </div>
</div>
@endsection
