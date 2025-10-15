@extends('layouts.app')

@section('title', 'Редактировать категорию')

@section('content')
<div class="container">
    <h1 class="mb-4">Редактировать категорию</h1>

   <form method="GET" action="{{ route('categories.edit', $category->id) }}" class="mb-3">
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
<form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- скрытое поле: дочерняя категория --}}
    <div class="mb-3">
        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id" class="form-select">
            <option value="">Выберите категорию</option>
            @foreach($childCategories as $id => $childName)
                <option value="{{ $id }}"
                    {{ (int) (old('category_id') ?? $category->category_id) === (int)$id ? 'selected' : '' }}>
                    {{ $childName }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection