@extends('layouts.app')

@section('title', 'Добавить категорию')

@section('content')
<div class="container">
    <h1 class="mb-4">Добавить категорию</h1>

    {{-- Вывод ошибок --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория</label>
            <select name="parent_id" id="parent_id" class="form-select">
                <option value="">Выберите или оставьте пустым для главной категории</option>
                @foreach($parents as $id => $name)
                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Название категории</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Добавить категорию</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection