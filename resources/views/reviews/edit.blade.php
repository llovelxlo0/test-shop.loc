@extends('layouts.app')

@section('title', 'Редактировать отзыв')

@section('content')
<div class="container mt-4">
    <h2>Редактировать отзыв</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="comment" class="form-label">Текст отзыва</label>
            <textarea name="comment" id="comment" rows="4" class="form-control" required>{{ old('comment', $review->comment) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Текущая картинка</label><br>
            @if($review->image)
                <img src="{{ asset('storage/' . $review->image) }}" width="150" class="mb-2">
            @else
                <p class="text-muted">Без изображения</p>
            @endif
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Заменить фото (необязательно)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Сохранить изменения</button>
        <a href="{{ route('goods.info', $review->goods_id) }}" class="btn btn-secondary">Отмена</a>
    </form>
</div>
@endsection
