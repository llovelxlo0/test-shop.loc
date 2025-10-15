@extends('layouts.app')

@section('title', 'Редактировать товар')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать товар</h1>

{{-- GET форма для смены выбранного родителя --}}
<form method="GET" action="{{ route('goods.edit', $good->id) }}" class="mb-3">
    <select name="parent_id" onchange="this.form.submit()" class="form-select">
        <option value="">Выберите родителя</option>
        @foreach($parents as $id => $name)
            <option value="{{ $id }}"
                {{ (int) (old('parent_id') ?? $selectedParentId) === (int)$id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</form>

{{-- Form update --}}
<form method="POST" action="{{ route('goods.update', $good->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- скрытое поле: дочерняя категория --}}
    <div class="mb-3">
        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id" class="form-select">
            <option value="">Выберите категорию</option>
            @foreach($childCategories as $id => $childName)
                <option value="{{ $id }}"
                    {{ (int) (old('category_id') ?? $good->category_id) === (int)$id ? 'selected' : '' }}>
                    {{ $childName }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

        {{-- Остальные поля --}}
        <div class="mb-3">
            <label for="name" class="form-label">Название товара</label>
            <input type="text" name="name" class="form-control"
                   id="name" value="{{ old('name', $good->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea name="description" id="description" class="form-control"
                      rows="4">{{ old('description', $good->description) }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" name="price" id="price" step="0.01"
                   class="form-control" value="{{ old('price', $good->price) }}">
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Картинка</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Количество на складе</label>
            <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', $good->stock ?? 0) }}" class="form-control">
            @error('stock')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Обновить</button>
        <a href="{{ route('goods.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection
