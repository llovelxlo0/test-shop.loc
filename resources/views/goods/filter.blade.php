@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Фильтрация товаров</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="parentCategory">Родительская категория:</label>
            <select id="parentCategory" class="form-select">
                <option value="">Выберите...</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="childCategory">Подкатегория:</label>
            <select id="childCategory" class="form-select" disabled>
                <option value="">Сначала выберите родителя</option>
            </select>
        </div>
    </div>

    <div id="goodsList" class="row mt-4">
        {{-- Сюда динамически подгружаются товары --}}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const parentSelect = document.getElementById('parentCategory');
    const childSelect = document.getElementById('childCategory');
    const goodsList = document.getElementById('goodsList');

    //  Выбор родительской категории
    parentSelect.addEventListener('change', async () => {
        const parentId = parentSelect.value;

        childSelect.innerHTML = '<option value="">Загрузка...</option>';
        childSelect.disabled = true;

        if (parentId) {
            const response = await fetch(`/categories/${parentId}/subcategories`);
            const data = await response.json();

            childSelect.innerHTML = '<option value="">Выберите подкатегорию</option>';
            data.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                childSelect.appendChild(opt);
            });

            childSelect.disabled = false;
            fetchGoods({ parent_id: parentId });
        } else {
            goodsList.innerHTML = '';
        }
    });

    //  Выбор подкатегории
    childSelect.addEventListener('change', () => {
        const subId = childSelect.value;
        const parentId = parentSelect.value;
        fetchGoods({ parent_id: parentId, subcategory_id: subId });
    });

    //  Загрузка товаров по фильтрам
    async function fetchGoods(params) {
        const url = new URL('/goods/filter', window.location.origin);
        Object.entries(params).forEach(([key, val]) => {
            if (val) url.searchParams.append(key, val);
        });

        const response = await fetch(url);
        const goods = await response.json();

        goodsList.innerHTML = goods.length
            ? goods.map(g => `
                <div class="col-md-3 mb-3">
                    <div class="card p-2">
                        <img src="/storage/${g.image}" class="card-img-top" alt="${g.name}">
                        <div class="card-body">
                            <h5 class="card-title">${g.name}</h5>
                            <p>${g.price}₴</p>
                        </div>
                    </div>
                </div>
            `).join('')
            : '<p>Нет товаров по выбранным фильтрам.</p>';
    }
});
</script>
@endsection
