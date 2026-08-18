<x-layouts.public title="Log in - CropDetec">
    <section class="auth-card">
        <h1>Log in</h1>
        <p>Enter your farmer account details to open the browser dashboard.</p>

        <form method="post" action="/login">
            @csrf
            <div>
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <button type="submit">Log in</button>
        </form>

        <p>Need an account? <a href="/register">Create one</a>.</p>
    </section>
</x-layouts.public>
