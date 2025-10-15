@extends('layouts.app')

@section('title', $goods->name)

@section('content')
<div class="container">
    {{-- Если есть категория --}}
    @if($goods->category->parent)
            <p><strong>Категория:</strong> {{ $goods->category->parent->name }}</p> 
        @if ($goods->category)
        <p><strong>Тип Продукта:</strong> {{ $goods->category->name }}</p>
        @endif
    @endif
    <h1>{{ $goods->name }}</h1>
    @if ($goods->image)
        <img src="{{ asset('storage/' . $goods->image) }}" alt="{{ $goods->name }}" style="max-width: 400px;">
    @endif
    <p><strong>Описание:</strong> {{ $goods->description }}</p>
    <p><strong>Цена:</strong> {{ number_format($goods->price, 2) }}</p>
    <p><strong>Количество на складе:</strong> {{ $goods->stock }}</p>
    <a href="{{ route('goods.index') }}" class="btn btn-secondary">Назад к списку</a>
</div>
@endsection
