@props(['review'])

@php
    /** @var \App\Models\Review $review */
    $user = Auth::user();
    $canModify = $user && $user->id === $review->user_id;
@endphp

<div class="border rounded p-3 mb-3">
    <div class="d-flex justify-content-between">
        <strong>{{ $review->user->name ?? 'Пользователь' }}</strong>
        <small class="text-muted">
            {{ $review->created_at->format('d.m.Y H:i') }}
        </small>
    </div>

    {{-- Кнопки "Редактировать / Удалить" только для автора --}}
    @if($canModify)
        <div class="d-flex gap-2 mt-2">
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
