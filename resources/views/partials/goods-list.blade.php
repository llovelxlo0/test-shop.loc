@if($goods->count())
    @foreach($goods as $good)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <x-product-card :goods="$good" />
        </div>
    @endforeach
@else
    <div class="col-12">
        <p class="text-muted">Товары не найдены</p>
    </div>
@endif
