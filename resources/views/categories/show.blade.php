@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container">
    <h1>{{ $category->name }}</h1>
    <p><strong>Категория:</strong> {{ optional($category->parent)->name ?? '—' }}</p>

    @if ($category->children->isNotEmpty())
        <h2>Тип категории</h2>
        <ul>
            @foreach ($category->children as $child)
                <li>{{ $child->name }}</li>
            @endforeach
        </ul>
    @endif
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Назад к списку</a>
</div>
@endsection
</