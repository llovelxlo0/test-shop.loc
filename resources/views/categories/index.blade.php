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
                <th>Количество на складе</th>
                <th>Корзина</th>
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
                    <td>
                        <a href="{{ route('goods.info', $category->id) }}">
                            {{ $category->name }}
                        </a>
                    </td>
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
                        @if($category->stock !== null)
                            {{ $category->stock }}
                        @else
                            <span>—</span>
                        @endif
                    </td>
                    <td>
                        @if($category->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="goods_id" value="{{ $category->id }}">
                                <button type="submit" class="btn btn-success btn-sm">В корзину</button>
                            </form>
                        @else
                            <span class="text-danger">Нет в наличии</span>
                        @endif
                    </td>
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
