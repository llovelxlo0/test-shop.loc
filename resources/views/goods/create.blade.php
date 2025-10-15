@extends('layouts.app')

@section('title', 'Добавить товар')

@section('content')
<div class="container">
    <h1 class="mb-4">Добавить товар</h1>

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

    <form method="GET" action="{{ route('goods.create') }}">
        <div class="mb-3">
            <label for="parent_id" class="form-label">Категория</label>
            <select name="parent_id" id="parent_id" class="form-select" onchange="this.form.submit()">
                <option value="">Выберите категорию</option>
                @foreach($parents as $id => $name)
                    <option value="{{ $id }}" {{ $selectedParentId == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Основная форма создания категории/товара --}}
    @if(isset($childCategories) && $childCategories->isNotEmpty())
        <form method="POST" action="{{ route('goods.store') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="parent_id" value="{{ $selectedParentId }}">

            <div class="mb-3">
                <label for="category_id" class="form-label">Тип категории</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Выберите тип категории</option>
                    @foreach($childCategories as $id => $name)
                        <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Название товара</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="{{ old('name') }}" placeholder="Введите название товара" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea name="description" id="description" class="form-control"
                          rows="4" placeholder="Введите описание категории">{{ old('description') }}</textarea>
                @error('description') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена</label>
                <input type="number" name="price" id="price" step="0.01"
                       class="form-control" value="{{ old('price') }}" placeholder="Введите цену">
                @error('price') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Картинка</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                @error('image') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Количество на складе</label>
                <input type="number" name="stock" id="stock" class="form-control"
                       min="0" value="{{ old('stock', 0) }}">
                @error('stock') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-success">Добавить</button>
            <a href="{{ route('goods.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    @endif
</div>
@endsection
