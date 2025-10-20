@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Категории</h1>
    @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Добавить категорию</a>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Тип Категории</th>
                <th>Название Категории</th>
                @if(Auth::check() && Auth::user()->isAdmin())
                    <th>Действия</th>
                @endif
            </tr>
        </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
        <td>{{ $category->id }}</td>
          <td>{{ $category->name }}</td>
          <td>{{optional($category->parent)->name ?? '—'}}</td>
          @if(Auth::check() && Auth::user()->isAdmin())
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