@extends('layouts.app')

@section('title', 'Редактировать товар')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать товар</h1>

    {{-- Ошибки --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('goods.update', $good->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Категория --}}
        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория</label>
            <select name="parent_id" id="parent_id" class="form-select" disabled>
                @foreach($parents as $id => $name)
                    <option value="{{ $id }}" {{ $selectedParentId == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Подкатегория --}}
        <div class="mb-3">
            <label for="category_id" class="form-label">Подкатегория</label>
            <select name="category_id" id="category_id" class="form-select" disabled>
                @foreach($childCategories as $id => $name)
                    <option value="{{ $id }}" {{ $good->category_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Основные поля --}}
        <div class="mb-3">
            <label for="name" class="form-label">Название товара</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name', $good->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea name="description" id="description" class="form-control"
                      rows="4">{{ old('description', $good->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control"
                   value="{{ old('price', $good->price) }}">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Количество на складе</label>
            <input type="number" name="stock" id="stock" class="form-control"
                   value="{{ old('stock', $good->stock) }}" min="0">
        </div>

        {{-- Картинка --}}
        <div class="mb-3">
            <label class="form-label">Картинка</label><br>
            @if($good->image)
                <img src="{{ asset('storage/' . $good->image) }}" width="100" class="mb-2"><br>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        {{--  ФИКСИРОВАННЫЕ характеристики --}}
        @php
            $categoryAttributes = $good->category->attributes ?? collect();
        @endphp

        @if($categoryAttributes->isNotEmpty())
            <h5 class="mt-4">Характеристики подкатегории</h5>
            @foreach($categoryAttributes as $attr)
                @php
                    $existingValue = $good->attributes->firstWhere('id', $attr->id)?->pivot->value;
                @endphp
                <div class="mb-2">
                    <label>{{ ucfirst($attr->name) }}</label>
                    <input type="text" name="attributes[{{ $attr->id }}][value]"
                           value="{{ old("attributes.{$attr->id}.value", $existingValue) }}"
                           class="form-control" placeholder="Введите значение">
                </div>
            @endforeach
        @endif

        {{--  КАСТОМНЫЕ характеристики --}}
        <div id="custom-attributes-container" class="mt-4">
            <h5>Дополнительные характеристики</h5>
            <button type="button" id="add-custom-attribute" class="btn btn-outline-primary btn-sm mb-2">
                + Добавить характеристику
            </button>

            @foreach($good->attributes->filter(fn($a) => !$categoryAttributes->contains('id', $a->id)) as $custom)
                <div class="attribute-row mb-2 d-flex">
                    <input type="text" name="attributes[new_{{ $loop->index }}][name]"
                           value="{{ $custom->name }}" class="form-control me-2">
                    <input type="text" name="attributes[new_{{ $loop->index }}][value]"
                           value="{{ $custom->pivot->value }}" class="form-control">
                    <button type="button" class="btn btn-danger btn-sm ms-2 remove-attribute">🗑</button>
                </div>
            @endforeach
        </div>

        {{-- Кнопки --}}
        <button type="submit" class="btn btn-success mt-3">💾 Сохранить</button>
        <a href="{{ route('goods.index') }}" class="btn btn-secondary mt-3">← Назад</a>
    </form>
</div>

{{-- JS для добавления кастомных атрибутов --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    let counter = {{ $good->attributes->count() }};
    document.getElementById('add-custom-attribute').addEventListener('click', () => {
        const container = document.getElementById('custom-attributes-container');
        const div = document.createElement('div');
        div.classList.add('attribute-row', 'mb-2', 'd-flex');
        div.innerHTML = `
            <input type="text" name="attributes[new_${counter}][name]" class="form-control me-2" placeholder="Название">
            <input type="text" name="attributes[new_${counter}][value]" class="form-control" placeholder="Значение">
            <button type="button" class="btn btn-danger btn-sm ms-2 remove-attribute">🗑</button>
        `;
        container.appendChild(div);
        counter++;

        div.querySelector('.remove-attribute').addEventListener('click', () => div.remove());
    });

    document.querySelectorAll('.remove-attribute').forEach(btn => {
        btn.addEventListener('click', e => e.target.closest('.attribute-row').remove());
    });
});
</script>
@endsection
