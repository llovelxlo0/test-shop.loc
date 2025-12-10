@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
<div class="container mt-4">
    <h2>Товары</h2>

    {{-- Фильтр --}}
<form method="GET" action="{{ route('goods.index') }}" id="categoryForm" class="row mb-3">
    <div class="col-md-4">
        <label for="parent_id">Категория:</label>
        <select id="parent_id" name="parent_id" class="form-select">
            <option value="">Все категории</option>
            @foreach($tree as $parentName => $children)
                @php
                    // Берём id родителя по имени (как ты уже делал)
                    $parentId = \App\Models\Category::where('name', $parentName)->value('id');
                @endphp
                <option value="{{ $parentId }}"
                        {{ (int)request('parent_id') === $parentId ? 'selected' : '' }}>
                    {{ $parentName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="subcategory_id">Подкатегория:</label>
        <select id="subcategory_id" name="subcategory_id" class="form-select">
            <option value="">Все подкатегории</option>
            @php
                $selectedParentId = request('parent_id');
            @endphp

            @if($selectedParentId)
                @php
                    $parent = \App\Models\Category::find($selectedParentId);
                @endphp
                @if($parent)
                    @foreach($parent->children as $child)
                        <option value="{{ $child->id }}"
                                {{ (int)request('subcategory_id') === $child->id ? 'selected' : '' }}>
                            {{ $child->name }}
                        </option>
                    @endforeach
                @endif
            @endif
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
                class="btn btn-outline-secondary"
                {{-- Если атрибутов пока нет – блокируем кнопку --}}
                {{ (isset($attributesForFilter) && $attributesForFilter->count()) ? '' : 'disabled' }}>
            Расширенный фильтр
        </button>
    </div>
</form>
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

            <button type="submit" class="btn btn-sm btn-primary">
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
    <div id="goodsList" class="row mt-4">
    @foreach($goods as $good)
        <div class="col-md-3 mb-3">
            <a href="{{ route('goods.info', $good->id) }}"
               class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100">
                    <img src="/storage/{{ $good->image }}" class="card-img-top" alt="{{ $good->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $good->name }}</h5>
                        <p class="card-text">{{ $good->price }}₴</p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>


{{-- 📦 Встраиваем JSON с категориями в безопасный блок --}}
<script id="categories-data" type="application/json">
    {!! json_encode($tree) !!}
</script>
{{-- JSON с категориями (должен быть ДО основного JS!) --}}
    @if(isset($tree))
    <script id="categories-data" type="application/json">
        {!! json_encode($tree) !!}
    </script>
    @endif

    {{-- Основной JS --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const raw = document.getElementById('categories-data')?.textContent;
        if (!raw) return; // нет категорий — выходим

        const tree = JSON.parse(raw);

        // ловим клики по категориям
        document.querySelectorAll('.dropdown-item').forEach(el => {
            el.addEventListener('click', e => {
                const parent = e.target.closest('.dropdown-submenu')
                    ?.querySelector('.dropdown-toggle')
                    ?.textContent?.trim();
                const subcategory = e.target.textContent.trim();

                e.preventDefault();
                filterGoods(parent, subcategory);
            });
        });

        async function filterGoods(parentName, subcategoryName) {
            // получаем id родителя и подкатегории
            const parentId = Object.keys(tree).find(
                p => p.toLowerCase() === parentName?.toLowerCase()
            )
                ? Object.keys(tree).indexOf(parentName) + 1
                : null;

            let subcategoryId = null;
            if (subcategoryName && parentName) {
                const parentChildren = tree[parentName];
                subcategoryId = Object.entries(parentChildren).find(([id, name]) =>
                    name.toLowerCase() === subcategoryName.toLowerCase()
                )?.[0];
            }

            // создаём ссылку с параметрами
            const url = new URL('/goods', window.location.origin);
            if (parentId) url.searchParams.append('parent_id', parentId);
            if (subcategoryId) url.searchParams.append('subcategory_id', subcategoryId);

            // редирект на страницу фильтрации
            window.location.href = url.toString();
        }
    });
    </script>

{{-- Основной JS-код --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const categoryForm   = document.getElementById('categoryForm');
    const parentSelect   = document.getElementById('parent_id');
    const childSelect    = document.getElementById('subcategory_id');
    const toggleAdvanced = document.getElementById('toggleAdvanced');
    const advancedBlock  = document.getElementById('advancedFilters');

    if (parentSelect && childSelect && categoryForm) {
        // При смене родительской категории:
        parentSelect.addEventListener('change', () => {
            // Сбрасываем подкатегорию и отправляем форму
            childSelect.value = '';
            categoryForm.submit();
        });

        // При смене подкатегории сразу отправляем форму
        childSelect.addEventListener('change', () => {
            categoryForm.submit();
        });
    }

    if (toggleAdvanced && advancedBlock) {
        toggleAdvanced.addEventListener('click', () => {
            advancedBlock.classList.toggle('d-none');
        });
    }
});
</script>
@endsection
