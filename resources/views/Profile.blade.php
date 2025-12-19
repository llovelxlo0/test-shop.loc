@extends('layouts.app')

@section('title', 'Настройки профиля')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Настройки профиля</h2>

    {{-- Уведомления --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
    Уведомления
    @if(auth()->user()->unreadNotifications->count())
        <span class="badge bg-danger">
            {{ auth()->user()->unreadNotifications->count() }}
        </span>
    @endif
    </a>
    @auth
    <a href="{{ route('profile.orders.index') }}" class="btn btn-outline-primary">
        История покупок
    </a>

    @can('viewAny', \App\Models\Order::class)
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark ms-2">
            Все заказы
        </a>
    @endcan
@endauth

    {{-- === Редактирование профиля === --}}
    <form method="post" action="{{ route('profile.edit') }}" class="mb-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" id="name" name="name"
                   class="form-control"
                   value="{{ old('name', $user->name) }}">
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email"
                   class="form-control"
                   value="{{ old('email', $user->email) }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Новый пароль (оставь пустым, чтобы не менять)</label>
            <input type="password" id="password" name="password" class="form-control">
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Подтверждение нового пароля</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Обновить профиль</button>
    </form>

    {{-- === Двухфакторная аутентификация === --}}
    @if($user->twoFactor && $user->twoFactor->enabled)
        <h3 class="h5">Двухфакторная аутентификация включена</h3>

        <form method="POST" action="{{ route('2fa.disable') }}" class="mt-2 mb-4">
            @csrf
            @method('DELETE')
            <label for="otp" class="form-label">Введите 2FA-код для отключения</label>
            <input type="text" name="otp" id="otp" maxlength="6" required class="form-control w-auto">
            @error('otp')
                <p class="text-danger mt-1">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn btn-danger mt-2">Отключить 2FA</button>
        </form>
    @else
        @if (isset($qrCodeUrl) && $qrCodeUrl)
            <div class="mb-3">
                <p>📱 Отсканируйте QR:</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrCodeUrl) }}&size=200x200" alt="QR Code">
                <p class="mt-2">Или добавьте вручную ключ:</p>
                <code>{{ $secret }}</code>
            </div>

            <form method="post" action="{{ route('2fa.verifySetup') }}" class="mb-2">
                @csrf
                <label for="otp" class="form-label">Введите код из приложения:</label>
                <input type="text" name="otp" id="otp" maxlength="6" required class="form-control w-auto">
                <button type="submit" class="btn btn-success mt-2">Подтвердить и включить 2FA</button>
            </form>

            <form method="post" action="{{ route('2fa.disable') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary">Отмена</button>
            </form>
        @else
            <form method="get" action="{{ route('2fa.setup') }}" class="mb-4">
                <button type="submit" class="btn btn-outline-primary">Включить 2FA</button>
            </form>
        @endif
    @endif

    {{-- === Избранные товары === --}}
    <hr class="my-4">

    <h2 class="h4 mb-3">Мои избранные товары</h2>

    @if($user->wishlist->count())
    <div class="row mt-3">
        @foreach($user->wishlist as $good)
            <x-product-card :goods="$good" :showCategory="true" />
        @endforeach
    </div>
    @else
        <p class="text-muted">У вас пока нет избранных товаров.</p>
    @endif

    <div class="mt-4">
        <a href="{{ route('home') }}" class="btn btn-link">⬅ Назад на главную</a>
    </div>
</div>
@endsection
