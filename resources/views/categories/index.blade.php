@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Категории</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(Auth::user()->usertype === 'admin')
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Добавить категорию</a>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Тип Категории</th>
                <th>Название Категории</th>
                <th>Название Продукта</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Картинка</th>
                @if(Auth::user()->usertype === 'admin')
                    <th>Действия</th>
                @endif
            </tr>
        </thead>
        <tbody>
            
            
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ optional($category->category->parent)->name ?? '—' }}</td>
                    <td>{{ $category->category->name ?? '—' }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ number_format($category->price, 2) }}</td>
                <td>
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" width="100" alt="Image">
                    @else
                        <span>Нет изображения</span>
                    @endif
                </td>
                <td>
                    @if(Auth::user()->usertype === 'admin')
                    
                    <td class="text-center align-middle">
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Удалить категорию?')">Удалить</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
