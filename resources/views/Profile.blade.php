<x-layout>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
    </head>
    <body>
        <h1>Profile Page</h1>

        @if (session('success'))
            <div role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="post" action="{{ route('profile.edit') }}">
            @csrf
            @method('PUT')
            <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name')
                    <span role="alert">{{ $message }}</span>
                @enderror
            <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                @error('email')
                    <span role="alert">{{ $message }}</span>
                @enderror
            <label for="password">New Password (leave blank to keep current password)</label>
                <input type="password" id="password" name="password">
                @error('password')
                    <span role="alert">{{ $message }}</span>
                @enderror
            <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
            <div>
                <button type="submit">Update Profile</button>
            </div>
            <div>
                <a href="{{ route('home') }}" class="btn btn-primary">Back</a>
            </div>
        </form>

        
    </body>
    </html>
</x-layout>
