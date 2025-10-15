@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Товар</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('goods.create') }}" class="btn btn-primary">Добавить категорию</a>
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
                @if(Auth::check() && Auth::user()->isAdmin())
                    <th>Действия</th>
                @endif
            </tr>
        </thead>
        <tbody>
            
            @foreach($goods as $good)
                <tr>
        <td>{{ $good->id }}</td>
        <td>{{ optional($good->category->parent)->name ?? '—' }}</td>
        <td>{{ $good->category->name ?? '—' }}</td>
        <td>
            <a href="{{ route('goods.info', $good->id) }}">
                {{ $good->name }}
            </a>
        </td>
        <td>{{ $good->description }}</td>
        <td>{{ number_format($good->price, 2) }}</td>
        <td>
            @if($good->image)
                <img src="{{ asset('storage/' . $good->image) }}" width="100" alt="Image">
            @else
                <span>Нет изображения</span>
            @endif
        </td>
        <td>
            @if($good->stock !== null)
                {{ $good->stock }}
            @else
                <span>—</span>
            @endif
        </td>
        <td>
            @if($good->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="goods_id" value="{{ $good->id }}">
                    <button type="submit" class="btn btn-success btn-sm">В корзину</button>
                </form>
            @else
                <span class="text-danger">Нет в наличии</span>
            @endif
        </td>
        @if(Auth::check() && Auth::user()->isAdmin())
            <td class="text-center align-middle">
                <a href="{{ route('goods.edit', $good->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                <form action="{{ route('goods.destroy', $good->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Удалить товар?')">Удалить</button>
                </form>
            </td>
        @endif
    </tr>
@endforeach
        </tbody>
    </table>
</div>
@endsection
