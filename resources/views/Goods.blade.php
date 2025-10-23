@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
<div class="container mt-4">
    <h2>Товары</h2>

    {{-- Фильтр --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="parentCategory">Категория:</label>
            <select id="parentCategory" class="form-select">
                <option value="">Все категории</option>
                @foreach($tree as $parentName => $children)
                    <option value="{{ \App\Models\Category::where('name', $parentName)->first()->id }}">
                        {{ $parentName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="childCategory">Подкатегория:</label>
            <select id="childCategory" class="form-select" disabled>
                <option value="">Сначала выберите категорию</option>
            </select>
        </div>
    </div>

    {{-- Контейнер товаров --}}
    <div id="goodsList" class="row mt-4">
        @foreach($goods as $good)
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <img src="/storage/{{ $good->image }}" class="card-img-top" alt="{{ $good->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $good->name }}</h5>
                        <p class="card-text">{{ $good->price }}₴</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- 📦 Встраиваем JSON с категориями в безопасный блок --}}
<script id="categories-data" type="application/json">
    {!! json_encode($tree) !!}
</script>

{{-- Основной JS-код --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const parentSelect = document.getElementById('parentCategory');
    const childSelect = document.getElementById('childCategory');
    const goodsList = document.getElementById('goodsList');

    // 🟦 1. Получаем категории из JSON-скрипта
    const raw = document.getElementById('categories-data').textContent;
    const tree = JSON.parse(raw);

    // 🟩 2. При выборе родительской категории
    parentSelect.addEventListener('change', () => {
        const parentId = parentSelect.value;
        const parentName = parentSelect.options[parentSelect.selectedIndex]?.text;
        const children = tree[parentName] || {};

        childSelect.innerHTML = '<option value="">Все подкатегории</option>';
        Object.entries(children).forEach(([id, name]) => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = name;
            childSelect.appendChild(opt);
        });
        childSelect.disabled = Object.keys(children).length === 0;

        fetchGoods({ parent_id: parentId });
    });

    // 🟨 3. При выборе подкатегории
    childSelect.addEventListener('change', () => {
        fetchGoods({
            parent_id: parentSelect.value,
            subcategory_id: childSelect.value
        });
    });

    // 🔶 4. Функция подгрузки товаров
    async function fetchGoods(params) {
        const url = new URL('/goods', window.location.origin);
        Object.entries(params).forEach(([key, value]) => {
            if (value) url.searchParams.append(key, value);
        });

        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const goods = await response.json();

        goodsList.innerHTML = goods.length
        ? goods.map(g => `
        <div class="col-md-3 mb-3">
            <a href="/goods/${g.id}/info" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100">
                    <img src="/storage/${g.image}" class="card-img-top" alt="${g.name}">
                    <div class="card-body">
                        <h5 class="card-title">${g.name}</h5>
                        <p class="card-text">${g.price}₴</p>
                    </div>
                </div>
            </a>
        </div>
    `).join('')
    : '<p class="text-muted">Нет товаров по выбранным фильтрам.</p>';
    }
});
</script>
@endsection
