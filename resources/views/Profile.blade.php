<x-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>

    <h1>Настройки профиля</h1>

    {{-- Уведомления --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- === Редактирование профиля === --}}
    <form method="post" action="{{ route('profile.edit') }}">
        @csrf
        @method('PUT')

        <label for="name">Имя</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
        @error('name') <small>{{ $message }}</small> @enderror

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
        @error('email') <small>{{ $message }}</small> @enderror

        <label for="password">Новый пароль (оставь пустым, чтобы не менять)</label>
        <input type="password" id="password" name="password">
        @error('password') <small>{{ $message }}</small> @enderror

        <label for="password_confirmation">Подтверждение нового пароля</label>
        <input type="password" id="password_confirmation" name="password_confirmation">

        <button type="submit">Обновить профиль</button>
    </form>

    {{-- === Двухфакторная аутентификация === --}}
    @if($user->twoFactor && $user->twoFactor->enabled)
    <h3>Двухфакторная аутентификация включена</h3>

    <form method="POST" action="{{ route('2fa.disable') }}">
        @csrf
        @method('DELETE')
        <label for="otp">Введите 2FA-код для отключения</label>
        <input type="text" name="otp" id="otp" maxlength="6" required>
        @error('otp')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <button type="submit">Отключить 2FA</button>
    </form>
    @else
    @if (isset($qrCodeUrl) && $qrCodeUrl)
        <p>📱 Отсканируйте QR:</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrCodeUrl) }}&size=200x200" alt="QR Code">
        <p>Или добавьте вручную ключ:</p>
        <code>{{ $secret }}</code>

        <form method="post" action="{{ route('2fa.verifySetup') }}">
            @csrf
            <label for="otp">Введите код из приложения:</label>
            <input type="text" name="otp" id="otp" maxlength="6" required>
            <button type="submit">Подтвердить и включить 2FA</button>
        </form>

        <form method="post" action="{{ route('2fa.disable') }}">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:#6b7280;">Отмена</button>
        </form>
        @else
        <form method="get" action="{{ route('2fa.setup') }}">
            <button type="submit">Включить 2FA</button>
        </form>
        @endif
    @endif

    <div style="margin-top:20px;">
        <a href="{{ route('home') }}">⬅ Назад на главную</a>
    </div>
</body>
</html>
</x-layout>
