@extends('layouts.app')

@section('title', 'Добавить категорию')

@section('content')
<div class="container">
    <h1 class="mb-4">Добавить новую категорию</h1>

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

    <form method="GET" action="{{ route('categories.create') }}">
    @csrf
    <div class="mb-3">
        <label for="parent_id">Категория</label>
        <select name="parent_id" id="parent_id" onchange="this.form.submit()">
            <option value="">Выберите Категорию</option>
            @foreach($parents as $id => $name)
                <option value="{{ $id }}"
                  {{ ($selectedParentId == $id) ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

@if(isset($childCategories) && $childCategories->isNotEmpty())
    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="parent_id" value="{{ $selectedParentId }}">

        <div class="mb-3">
            <label for="category_id">Тип Категория</label>
            <select name="category_id" id="category_id">
                <option value="">Выберите Тип категории</option>
                @foreach($childCategories as $id => $name)
                    <option value="{{ $id }}"
                      {{ old('category_id') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

            <div class="mb-3">
                <label for="name" class="form-label">Название товара</label>
                <input type="text" name="name" id="name" class="form-control"
                       placeholder="Введите название товара" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea name="description" id="description" class="form-control"
                          rows="4" placeholder="Введите описание категории"></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена</label>
                <input type="number" name="price" id="price" step="0.01"
                       class="form-control" placeholder="Введите цену">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Картинка</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-success">Добавить</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    @endif
</div>
@endsection
