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

        {{-- Товары с подменю --}}
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
              Товары
          </a>
          <ul class="dropdown-menu" aria-labelledby="productsDropdown">
            @if(!empty($tree))
              @foreach($tree as $parentName => $children)
                @if(count($children) > 0)
                  <li class="dropdown-submenu">
                    <a class="dropdown-item dropdown-toggle" href="#">
                      {{ ucfirst($parentName) }}
                    </a>
                    <ul class="dropdown-menu">
                      @foreach($children as $childId => $childName)
                        <li>
                          <a class="dropdown-item" href="{{ route('goods.info', $childId) }}">
                            {{ $childName }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </li>
                @endif
              @endforeach
            @endif
          </ul>
        </li>

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
    <div class="container">
        @yield('content')
    </div>

    {{-- Подключение Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
