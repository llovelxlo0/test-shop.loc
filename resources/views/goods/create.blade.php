@extends('layouts.app')

@section('title', 'Добавить товар')

@section('content')
<div class="container">
    <h1 class="mb-4">Добавить товар</h1>

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

    {{-- Выбор категории --}}
    <form method="GET" action="{{ route('goods.create') }}">
        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория</label>
            <select name="parent_id" id="parent_id" class="form-select" onchange="this.form.submit()">
                <option value="">Выберите категорию</option>
                @foreach($parents as $id => $name)
                    <option value="{{ $id }}" {{ $selectedParentId == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(isset($childCategories) && $childCategories->isNotEmpty())
            <div class="mb-3">
                <label for="category_id" class="form-label">Подкатегория</label>
                <select name="category_id" id="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Выберите подкатегорию</option>
                    @foreach($childCategories as $id => $name)
                        <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </form>

    {{-- Форма создания товара --}}
    @if(request('category_id'))
    <form method="POST" action="{{ route('goods.store') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="category_id" value="{{ request('category_id') }}">

        {{-- Основные поля --}}
        <div class="mb-3">
            <label class="form-label">Название товара</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Цена</label>
            <input type="number" step="0.01" name="price" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Количество</label>
            <input type="number" name="stock" class="form-control" min="0" value="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Изображение</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        {{--  ФИКСИРОВАННЫЕ характеристики --}}
        @if($categoryAttributes->isNotEmpty())
            <h5>Характеристики подкатегории</h5>
            @foreach($categoryAttributes as $attr)
                <div class="mb-2">
                    <label>{{ ucfirst($attr->name) }}</label>
                    <input type="text" name="attributes[{{ $attr->id }}][value]" class="form-control"
                           placeholder="Введите значение для {{ $attr->name }}">
                </div>
            @endforeach
        @endif

        {{--  КАСТОМНЫЕ характеристики --}}
        <div id="custom-attributes-container" class="mt-4">
            <h5>Дополнительные характеристики</h5>
            <button type="button" id="add-custom-attribute" class="btn btn-outline-primary btn-sm mb-2">
                + Добавить характеристику
            </button>
        </div>

        <button type="submit" class="btn btn-success mt-3">Добавить товар</button>
        <a href="{{ route('goods.index') }}" class="btn btn-secondary mt-3">Назад</a>
    </form>
    @endif
</div>

{{-- JS для динамического добавления кастомных атрибутов --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    let counter = 0;
    document.getElementById('add-custom-attribute')?.addEventListener('click', () => {
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
});
</script>
@endsection
