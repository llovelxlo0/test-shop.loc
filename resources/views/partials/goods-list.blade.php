<div class="container mt-4">
    <h2>Список товаров</h2>
    @if($goods->count())
        @foreach($goods as $good)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <x-product-card :goods="$good" />
            </div>
        @endforeach
    @else
        <div class="col-12">
            <p class="text-muted">Товары не найдены</p>
        </div>
@endif

