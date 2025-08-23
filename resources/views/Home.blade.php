
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <title>Test_Shop</title>
</head>
<body>
    <h1>Welcome Test Site</h1>
    <p>This is the home page of your application.</p>
    <div>
        @auth
            <p>Hello, {{ Auth::user()->name }}!</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn">Logout</button>
            </form>
        @else
    
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a> or
        <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        @endauth
        </div>
</body>
</html>
