@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
<div class="container mt-4">
    <h2>Товары</h2>

    {{-- Фильтр --}}
    <form method="GET" action="{{ route('goods.index') }}" id="categoryForm" class="row mb-3">
        <input type="hidden" name="apply" value="1">

        {{-- Родительская категория --}}
        <div class="col-md-4">
            <label for="parent_id">Категория:</label>
            <select name="parent_id" id="parent_id" class="form-select">
                <option value="">Все категории</option>
                @if(!empty($tree))
                @foreach($tree as $parent)
                    <option value="{{ $parent['id'] }}"
                        {{ request('parent_id') == $parent['id'] ? 'selected' : '' }}>
                        {{ $parent['name'] }}
                    </option>
                @endforeach
                @endif
            </select>
        </div>

        {{-- Подкатегории --}}
        <div class="col-md-4">
            <label for="subcategory_id">Подкатегория:</label>
            <select id="subcategory_id" name="subcategory_id" class="form-select">
                <option value="">Все подкатегории</option>
                    @foreach($tree as $parent)
                        @if((int)request('parent_id') === $parent['id'])
                            @if(!empty($parent['children']))
                            @foreach($parent['children'] as $child)
                                <option value="{{ $child['id'] }}"
                                    @selected((int)request('subcategory_id') === $child['id'])>
                                    {{ $child['name'] }}
                                </option>
                            @endforeach
                          @endif
                        @endif
                    @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
            {{-- Кнопка обычного фильтра по категории --}}
            <button type="submit" class="btn btn-primary">
                Показать
            </button>
            {{-- Кнопка, которая покажет / спрячёт расширенный фильтр --}}
            <button type="button"
                    id="toggleAdvanced"
                    class="btn btn-outline-secondary">
                Расширенный фильтр
            </button>
        </div>
    </form>
</div>
    {{-- Расширенный фильтр по характеристикам --}}
@if(isset($attributesForFilter) && $attributesForFilter->count())
    <div id="advancedFilters" class="border rounded p-3 mb-3 d-none">
        <h5>Расширенный фильтр по характеристикам</h5>

        <form method="GET" action="{{ route('goods.index') }}">
            {{-- Сохраняем выбранную категорию и подкатегорию --}}
            <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
            <input type="hidden" name="subcategory_id" value="{{ request('subcategory_id') }}">

            @foreach($attributesForFilter as $attr)
                <div class="mb-3">
                    <strong>{{ $attr->name }}</strong><br>

                    @foreach($attr->filter_values as $value)
                        @php
                            $isChecked = in_array(
                                $value,
                                $selectedAttributes[$attr->id] ?? []
                            );
                        @endphp
                        <label class="me-2">
                            <input type="checkbox"
                                   name="attributes[{{ $attr->id }}][]"
                                   value="{{ $value }}"
                                   {{ $isChecked ? 'checked' : '' }}>
                            {{ $value }}
                        </label>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" form="categoryForm" class="btn btn-sm btn-primary">
                Применить фильтр
            </button>
            <a href="{{ route('goods.index', ['parent_id' => request('parent_id'), 'subcategory_id' => request('subcategory_id')]) }}"
               class="btn btn-sm btn-link">
                Сбросить характеристики
            </a>
        </form>
    </div>
@endif

{{-- Контейнер товаров --}}
<div class="container mt-4">
    <h2>Список товаров</h2>

    <div id="goodsList" class="row g-3">
        @include('partials.goods-list', ['goods' => $goods])
    </div>
</div>>
{{-- 📦 Встраиваем JSON с категориями в безопасный блок --}}
    <script id="categories-data" type="application/json">
        {!! json_encode($tree, JSON_UNESCAPED_UNICODE) !!}
    </script>
    {{-- Основной JS --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const parentSelect = document.getElementById('parent_id');
        const subcategorySelect = document.getElementById('subcategory_id');

        parentSelect.addEventListener('change', function () {
            const parentId = this.value;
            subcategorySelect.innerHTML = '<option value="">Все подкатегории</option>';

            if (!parentId) return;

            fetch(`/categories/${parentId}/subcategories`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        subcategorySelect.appendChild(option);
                    });
                });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('categoryForm');
        const goodsList = document.getElementById('goodsList');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const params = new URLSearchParams(new FormData(form));

            const response = await fetch(`{{ route('goods.index') }}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            goodsList.innerHTML = await response.text();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleAdvanced = document.getElementById('toggleAdvanced');
        const advancedBlock  = document.getElementById('advancedFilters');

        toggleAdvanced?.addEventListener('click', () => {
            advancedBlock.classList.toggle('d-none');
    });
});
</script>
@endsection
