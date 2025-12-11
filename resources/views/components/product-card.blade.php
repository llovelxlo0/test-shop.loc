@props([
    'goods',                // Объект товара App\Models\Goods
    'showAddToCart' => true, // Показывать ли кнопку "В корзину"
    'compact' => false,      // Компактный режим (для похожих товаров, избранного и т.п.)
])

@php
    /** @var \App\Models\Goods $goods */
@endphp

<div class="card shadow-sm h-100">
    {{-- Вся карточка кликабельна и ведёт на полную информацию о товаре --}}
    <a href="{{ route('goods.info', $goods->id) }}"
       class="text-decoration-none text-dark">

        @if($goods->image)
            <img src="{{ asset('storage/' . $goods->image) }}"
                 class="card-img-top"
                 alt="{{ $goods->name }}">
        @endif

        <div class="card-body {{ $compact ? 'p-2 text-center' : '' }}">
            {{-- Название товара --}}
            <h6 class="card-title {{ $compact ? 'text-truncate mb-1' : '' }}"
                title="{{ $goods->name }}">
                {{ $goods->name }}
            </h6>

            {{-- Цена --}}
            <p class="card-text text-success fw-bold mb-0">
                {{ number_format($goods->price, 2) }} ₴
            </p>

            {{-- Остаток на складе (только в “обычном”, не компактном режиме) --}}
            @if(!$compact && !is_null($goods->stock))
                <small class="text-muted">
                    На складе: {{ $goods->stock }}
                </small>
            @endif
        </div>
    </a>

    {{-- Кнопка "В корзину" (если включена и есть остаток) --}}
    @if($showAddToCart && $goods->stock > 0)
        <div class="card-footer bg-transparent border-0 pt-0 pb-2">
            <form action="{{ route('cart.add') }}" method="POST" class="d-grid">
                @csrf
                <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                <button type="submit" class="btn btn-success btn-sm">
                    🛒 В корзину
                </button>
            </form>
        </div>
    @elseif($showAddToCart && $goods->stock <= 0)
        <div class="card-footer bg-transparent border-0 pt-0 pb-2">
            <span class="text-danger small">Нет в наличии</span>
        </div>
    @endif
</div>
