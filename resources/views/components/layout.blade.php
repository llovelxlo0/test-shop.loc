<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css">
    <link rel="stylesheet" href="styles.css">
    <title>1</title>
</head>
<body>
    @if (session ('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    {{ $slot }}
</body>
</html>