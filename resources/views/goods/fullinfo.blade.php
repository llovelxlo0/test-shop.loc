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
            {{-- EAV --}}
            @if($goods->attributes && $goods->attributes->count())
                <h5 class="mt-4">Характеристики</h5>
                <ul class="list-group mb-4">
                    @foreach($goods->attributes as $attribute)
                        <li class="list-group-item">
                            <strong>{{ $attribute->name }}:</strong> {{ $attribute->pivot->value }}
                        </li>
                    @endforeach
                </ul>
            @endif
            {{-- Кнопка добавления в избранное --}}
            <x-wishlist-button :goods="$goods" :is-in-wishlist="$isInWishlist" />
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
            @if(isset($relatedGoods) && $relatedGoods->isNotEmpty())
            <hr>
                <h4 class="mt-4">Похожие товары</h4>
                <div class="row">
                    @foreach($relatedGoods as $item)
                        <div class="col-md-2 mb-3">
                            <x-product-card
                                :goods="$item"
                                :compact="true"
                                :show-add-to-cart="false"
                            />
                        </div>
                    @endforeach
                </div>
            @endif
    {{-- История просмотров --}}
    <x-view-history
    :items="$viewHistory"
    title="История просмотров"/>

    {{-- Кнопки админа --}}
    @can('update', $goods)
    <a href="{{ route('admin.goods.edit', $goods) }}" class="btn btn-warning btn-sm">Редактировать</a>
    @endcan

    @can('delete', $goods)
        <form action="{{ route('admin.goods.destroy', $goods) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">Удалить</button>
        </form>
    @endcan


    @auth
    <hr class="mt-4">
    <h5>Оставить отзыв</h5>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('goods.reviews.store', $goods) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="rating" class="form-label">Рейтинг (1-5):</label>
            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Комментарий:</label>
            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Изображение (необязательно):</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Отправить отзыв</button>
    </form>
    @endauth
    <h3 class="mt-4">Отзывы</h3>

    {{-- Панель сортировки --}}
    <div class="mb-3">
            <span>Сортировать:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}"
                class="{{ $sort === 'date' ? 'fw-bold' : '' }}">
                    по дате
                </a>
                |
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}"
                class="{{ $sort === 'rating' ? 'fw-bold' : '' }}">
                    по полезности
                </a>
        </div>

        @forelse($reviews as $review)
            <x-review-item :review="$review" />
        @empty
            <p>Пока нет одобренных отзывов. Вы можете быть первым!</p>
        @endforelse
    <div class="mt-4">
        <a href="{{ route('goods.index') }}" class="btn btn-secondary">← Назад к каталогу</a>
    </div>
</div>
</div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.wishlist-btn');

    if (!buttons.length) {
        return;
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const url = btn.dataset.url;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                if (!response.ok) {
                    console.error('Wishlist error:', response.status);
                    alert('Ошибка при добавлении в избранное');
                    return;
                }

                const data = await response.json();

                if (data.status === 'added') {
                    btn.textContent = '❤️ В избранном';
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-danger');
                } else if (data.status === 'removed') {
                    btn.textContent = '⭐ В избранное';
                    btn.classList.add('btn-outline-warning');
                    btn.classList.remove('btn-danger');
                }
            } catch (e) {
                console.error('Wishlist exception:', e);
                alert('Произошла ошибка. Проверь консоль.');
            }
        });
    });
});
</script>
@endpush
@endsection
