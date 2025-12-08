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
            <button 
                type="button"
                class="btn btn-outline-warning wishlist-btn"
                data-url="{{ route('wishlist.toggle', $goods->id) }}"
            >
                ⭐ В избранное
            </button>
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
                    <a href="{{ route('goods.info', $item->id) }}" class="text-decoration-none text-dark">
                        <div class="card shadow-sm h-100">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->name }}">
                            @endif
                            <div class="card-body text-center p-2">
                                <h6 class="card-title text-truncate" title="{{ $item->name }}">{{ $item->name }}</h6>
                                <p class="text-success fw-bold mb-0">{{ number_format($item->price, 2) }} ₴</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
    @if(isset($viewHistory) && $viewHistory->isNotEmpty())
    <hr>
    <h4 class="mt-4">Вы недавно смотрели</h4>
    <div class="row">
        @foreach($viewHistory as $item)
            <div class="col-md-2 mb-3">
                <a href="{{ route('goods.info', $item->id) }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm h-100">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->name }}">
                        @endif
                        <div class="card-body text-center p-2">
                            <h6 class="card-title text-truncate" title="{{ $item->name }}">{{ $item->name }}</h6>
                            <p class="text-success fw-bold mb-0">
                                {{ number_format($item->price, 2) }} ₴
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    @endif


            {{-- Кнопки админа --}}
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="mt-4">
                    <a href="{{ route('goods.edit', $goods->id) }}" class="btn btn-warning me-2">✏️ Редактировать</a>
                    <form action="{{ route('goods.destroy', $goods->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить товар?')">
                            🗑 Удалить
                        </button>
                    </form>
                </div>
            @endif

            @auth
            <hr class="mt-4">
            <h5>Оставить отзыв</h5>
            @if(session('succsess'))
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
            <form action="{{ route('goods.reviews.store', $goods->id) }}" method="POST" enctype="multipart/form-data">
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
            {{-- Список отзывов --}}
            @if($goods->reviews->count())
                <hr class="mt-4">
                <h5>Отзывы о товаре</h5>
                @foreach($goods->reviews->sortByDesc('created_at') as $review)
            <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between">
                <strong>{{ $review->user->name ?? 'Пользователь' }}</strong>
                <small class="text-muted">
                    {{ $review->created_at->format('d.m.Y H:i') }}
                </small>
            </div>
            @auth
                @if(Auth::id() === $review->user_id)
                    <div class="d-flex gap-2">
                        <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-primary">
                            Редактировать
                        </a>

                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                              onsubmit="return confirm('Удалить отзыв?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Удалить
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
            <div>
                Рейтинг:
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $review->rating)
                        <span class="text-warning">★</span>
                    @else
                        <span class="text-secondary">☆</span>
                    @endif
                @endfor
            </div>

            <p class="mt-2 mb-2">{{ $review->comment }}</p>

            @if($review->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $review->image) }}" 
                         alt="Фото отзыва"
                         class="img-fluid rounded" style="max-width: 200px;">
                </div>
            @endif
        </div>
        @endforeach
        @else
            <hr class="mt-4">
            <p class="text-muted">Пока нет ни одного отзыва. Будьте первым!</p>
        @endif
            <div class="mt-4">
                <a href="{{ route('goods.index') }}" class="btn btn-secondary">← Назад к каталогу</a>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.wishlist-btn');

    if (!buttons.length) {
        // для дебага
        console.log('wishlist: нет кнопок на странице');
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

                // Если не авторизован — Laravel вернёт 302 на /login
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
                console.log('Wishlist response:', data);

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
@endsection
