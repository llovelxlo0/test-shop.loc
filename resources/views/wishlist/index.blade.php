@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<div class="container mt-4">
    <h2>Ваши избранные товары</h2>

    <div class="row mt-3">
        @forelse($items as $good)
            <div class="col-md-3">
                <div class="card mb-3">
                    <img src="/storage/{{ $good->image }}" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">{{ $good->name }}</h5>
                        <a href="{{ route('goods.info', $good->id) }}" class="btn btn-primary">Открыть</a>
                    </div>
                </div>
            </div>
        @empty
            <p>У вас пока нет избранных товаров.</p>
        @endforelse
    </div>

</div>
@endsection
