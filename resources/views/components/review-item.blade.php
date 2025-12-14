@props(['review'])

@php
    /** @var \App\Models\Review $review */
@endphp

<div class="border rounded p-3 mb-3">

    {{-- Заголовок отзыва --}}
    <div class="d-flex justify-content-between">
        <strong>{{ $review->user->name ?? 'Пользователь' }}</strong>
        <small class="text-muted">
            {{ $review->created_at->format('d.m.Y H:i') }}
        </small>
    </div>

    {{-- Кнопки управления ОТЗЫВОМ --}}
    <div class="mt-2 mb-2">
        @can('update', $review)
            <a href="{{ route('reviews.edit', $review) }}"
               class="btn btn-sm btn-primary">
                Редактировать отзыв
            </a>
        @endcan

        @can('delete', $review)
            <form action="{{ route('reviews.destroy', $review) }}"
                  method="POST"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    Удалить отзыв
                </button>
            </form>
        @endcan
    </div>

    {{-- Статус (только для админа) --}}
    @auth
        @if(auth()->user()->isAdmin())
            <div class="mt-1">
                <span class="badge
                    @if($review->isApproved()) bg-success
                    @elseif($review->isRejected()) bg-danger
                    @else bg-secondary
                    @endif">
                    @if($review->isApproved())
                        Одобрен
                    @elseif($review->isRejected())
                        Отклонён
                    @else
                        На модерации
                    @endif
                </span>
            </div>
        @endif
    @endauth

    {{-- Лайки --}}
    @auth
        @can('vote', $review)
            <form action="{{ route('reviews.vote', $review) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" name="value" value="1"
                        class="btn btn-sm btn-outline-success">
                    Полезно
                </button>
                <button type="submit" name="value" value="-1"
                        class="btn btn-sm btn-outline-danger">
                    Не полезно
                </button>
            </form>
        @endcan
    @endauth

    <div class="mt-1">
        Полезность: {{ $review->rating_score }}
    </div>

    {{-- Рейтинг --}}
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

    {{-- Текст --}}
    @if($review->comment)
        <p class="mt-2 mb-2">{{ $review->comment }}</p>
    @endif

    {{-- Картинка --}}
    @if($review->image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $review->image) }}"
                 alt="Фото отзыва"
                 class="img-fluid rounded"
                 style="max-width: 200px;">
        </div>
    @endif

    {{-- ========================= --}}
    {{-- ОТВЕТЫ НА ОТЗЫВ --}}
    {{-- ========================= --}}
    @if($review->replies->isNotEmpty())
        <div class="mt-3 ms-4 border-start ps-3">
            @foreach($review->replies as $reply)
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $reply->user->name ?? 'Пользователь' }}</strong>
                        <small class="text-muted">
                            {{ $reply->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>

                    <p class="mb-1">{{ $reply->comment }}</p>

                    <div class="d-flex gap-2">
                        @can('update', $reply)
                            <a href="{{ route('reviews.replies.edit', $reply) }}"
                               class="btn btn-sm btn-outline-primary">
                                Редактировать
                            </a>
                        @endcan

                        @can('delete', $reply)
                            <form action="{{ route('reviews.replies.destroy', $reply) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Удалить
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Форма ответа --}}
    @auth
        <div class="mt-3 ms-4">
            <form action="{{ route('reviews.replies.store', $review) }}" method="POST">
                @csrf
                <textarea name="comment"
                          class="form-control mb-2"
                          rows="2"
                          required
                          placeholder="Ответить на отзыв..."></textarea>
                <button class="btn btn-sm btn-outline-primary">
                    Ответить
                </button>
            </form>
        </div>
    @endauth
</div>
