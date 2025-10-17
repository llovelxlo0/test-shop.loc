@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<h2>Two-Factor Authentication</h2>

{{-- Ошибки --}}
@if ($errors->any())
    <div style="color: red; margin-bottom: 1em;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Форма для ввода кода --}}
<form method="POST" action="{{ route('2fa.login.verify') }}">
    @csrf

    <label for="otp">Введите код из приложения:</label><br>
    <input type="text" name="otp" id="otp" maxlength="6" required autofocus placeholder="code"><br><br>

    <button type="submit">Подтвердить</button>
</form>
@endsection
