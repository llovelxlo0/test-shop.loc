@extends('layouts.app')

@section('title', 'Добавить категорию')

@section('content')
<div class="container">
    <h2 class="mb-4">Добавить новую категорию</h2>

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

    {{-- Форма добавления категории --}}
    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="category_name" class="form-label">Название категории</label>
            <select name="category_name" id="category_name" class="form-select" required>
                <option value="" disabled selected>Выберите название категории</option>
                @foreach($categoryNames as $name)
                    <option value="{{ $name->id }}">{{ $name->name }}</option>
                @endforeach
            </select>
        <div class="mb-3">
            <label for="category_type" class="form-label">Тип категории</label>
            <select name="category_type" id="category_type" class="form-select" required>
                <option value="" disabled selected>Выберите тип категории</option>
                @foreach($categoryTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Название товара</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Введите название товара" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Введите описание категории"></textarea>
        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" name="price" id="price" step="0.01" class="form-control" placeholder="Введите цену">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Картинка</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-success">Добавить</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection
