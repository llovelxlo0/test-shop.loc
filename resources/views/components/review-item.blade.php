@props(['review'])

@php
    /** @var \App\Models\Review $review */
    $user = Auth::user();
@endphp

<div class="border rounded p-3 mb-3">
    <div class="d-flex justify-content-between">
        <strong>{{ $review->user->name ?? 'Пользователь' }}</strong>
        <small class="text-muted">
            {{ $review->created_at->format('d.m.Y H:i') }}
        </small>
    </div>

    {{-- Кнопки "Редактировать / Удалить" — по Policy --}}
    <div class="mt-2 mb-2">
        @can('update', $review)
            <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-primary">
                Редактировать
            </a>
        @endcan

        @can('delete', $review)
            <form action="{{ route('reviews.destroy', $review) }}"
                  method="POST"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    Удалить
                </button>
            </form>
        @endcan
    </div>
    {{-- Статус отзыва (для админа, чтобы видно было, что изменилось) --}}
    @auth
        @if(auth()->user()->isAdmin())
            <div class="mt-1">
                <span class="badge 
                    @if($review->isApproved()) bg-success 
                    @elseif($review->isRejected()) bg-danger 
                    @else bg-secondary 
                    @endif
                ">
                    @if($review->isApproved())
                        Одобрен
                    @elseif($review->isRejected())
                        Отклонен
                    @else
                        На модерации
                    @endif
                </span>
            </div>
        @endif
    @endauth
    @auth
        @can('vote', $review)
            <form action="{{ route('reviews.vote', $review) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" name="value" value="1" class="btn btn-sm btn-outline-success">
                    Полезно
                </button>
                <button type="submit" name="value" value="-1" class="btn btn-sm btn-outline-danger">
                    Не полезно
                </button>
            </form>
        @endcan
    @endauth

    <div class="mt-1">
        Полезность: {{ $review->rating_score }}
    </div>
    {{-- Рейтинг звёздами --}}
    <div class="mt-2">
        Рейтинг:
        @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $review->rating)
                <span class="text-warning">★</span>
            @else
                <span class="text-secondary">☆</span>
            @endif
        @endfor
    </div>

    {{-- Текст отзыва --}}
    @if($review->comment)
        <p class="mt-2 mb-2">{{ $review->comment }}</p>
    @endif

    {{-- Картинка к отзыву --}}
    @if($review->image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $review->image) }}"
                 alt="Фото отзыва"
                 class="img-fluid rounded"
                 style="max-width: 200px;">
        </div>
    @endif
</div>
