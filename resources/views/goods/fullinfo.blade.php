@extends('layouts.app')

@section('title', $goods->name)

@section('content')
<div class="container mt-4">
    <div class="row">
        {{-- Левая часть: изображение --}}
        <div class="col-md-5">
            @if($goods->image)
                <img src="{{ asset('storage/' . $goods->image) }}" alt="{{ $goods->name }}" class="img-fluid rounded shadow-sm">
            @else
                <div class="bg-light text-center p-5 rounded border">
                    <span class="text-muted">Нет изображения</span>
                </div>
            @endif
        </div>

        {{-- Правая часть: информация --}}
        <div class="col-md-7">
            <h2 class="mb-3">{{ $goods->name }}</h2>

            <p class="text-muted">
                Категория:
                {{ optional($goods->category->parent)->name ? optional($goods->category->parent)->name . ' → ' : '' }}
                {{ $goods->category->name ?? 'Без категории' }}
            </p>

            <p><strong>Описание:</strong><br>{{ $goods->description ?? 'Описание отсутствует' }}</p>

            <p class="fs-4 fw-bold text-success mb-4">{{ number_format($goods->price, 2) }} ₴</p>

            <p><strong>Количество на складе:</strong> {{ $goods->stock ?? '—' }}</p>

            {{-- Кнопка добавления в корзину --}}
            @if($goods->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                    <button type="submit" class="btn btn-success">
                        🛒 Добавить в корзину
                    </button>
                </form>
            @else
                <p class="text-danger">Нет в наличии</p>
            @endif

            {{-- Кнопки админа --}}
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="mt-4">
                    <a href="{{ route('goods.edit', $goods->id) }}" class="btn btn-warning me-2">✏️ Редактировать</a>
                    <form action="{{ route('goods.destroy', $goods->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить товар?')">
                            🗑 Удалить
                        </button>
                    </form>
                </div>
            @endif

            {{-- Кнопка "Назад" --}}
            <div class="mt-4">
                <a href="{{ route('goods.index') }}" class="btn btn-secondary">← Назад к каталогу</a>
            </div>
        </div>
    </div>
</div>
@endsection
