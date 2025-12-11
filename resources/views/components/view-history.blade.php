@props([
    // Коллекция товаров (Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection)
    'items',
    'title' => 'Вы недавно смотрели',
    'emptyText' => 'Вы ещё ничего не смотрели',
])

@if($items && $items->count())
    <section class="mt-4">
        <h4 class="mb-3">{{ $title }}</h4>

        <div class="row">
            @foreach($items as $good)
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <x-product-card
                        :goods="$good"
                        :compact="true"
                        :show-add-to-cart="false"
                    />
                </div>
            @endforeach
        </div>
    </section>
@else
    <section class="mt-4">
        <h5 class="mb-2">{{ $title }}</h5>
        <p class="text-muted mb-0">{{ $emptyText }}</p>
    </section>
@endif
