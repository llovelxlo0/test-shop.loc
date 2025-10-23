<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Мой магазин')</title>

    {{-- Bootstrap для стилей --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Подключение пользовательских стилей --}}
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

    {{-- Навигационная панель --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Zett.Shop</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    
                    {{-- Остальные пункты --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('goods.index') }}">Товар</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.view') }}">
                            Корзина
                            @if(session('cart') && count(session('cart')) > 0)
                                <span class="badge bg-success">{{ count(session('cart')) }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- Авторизация --}}
                    @auth
                        @if(Auth::user()->usertype === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">Категории</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('goods.create') }}">Добавить Товар</a>
                            </li>
                        @endif

                        <li class="nav-item d-flex align-items-center">
                            <a href="{{ route('profile') }}" class="btn btn-primary btn-sm me-2">Профиль</a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Выйти</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Войти</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endauth

                </ul>
            </div>
        </div>
    </nav>

    {{-- Контент страниц --}}
    <main class="container mt-5 pt-4">
        @yield('content')
    </main>

    {{-- Подключение Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

    {{-- Подключение дополнительных JS-файлов через @push --}}
    @stack('scripts')
</body>
</html>
