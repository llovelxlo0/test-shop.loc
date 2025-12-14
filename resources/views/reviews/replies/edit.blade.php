@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Редактировать ответ</h2>

    <form action="{{ route('reviews.replies.update', $reply) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Текст ответа</label>
            <textarea name="comment" class="form-control" rows="4" required>{{ old('comment', $reply->comment) }}</textarea>
            @error('comment')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Отмена</a>
    </form>
</div>
@endsection
