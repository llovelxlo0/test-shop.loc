@props([
    'goods',          // \App\Models\Goods
    'isInWishlist' => false,
])

@php
    $url = route('wishlist.toggle', $goods->id);
@endphp

<button
    type="button"
    class="btn wishlist-btn
        {{ $isInWishlist ? 'btn-danger' : 'btn-outline-warning' }}"
    data-url="{{ $url }}"
>
    @if($isInWishlist)
        ❤️ В избранном
    @else
        ⭐ В избранное
    @endif
</button>
