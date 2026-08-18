<x-layouts.public title="Sign up - CropDetec">
    <section class="auth-card">
        <h1>Create account</h1>
        <p>Register as a farmer to access the browser dashboard. The same account can be used by the mobile app API.</p>

        <form method="post" action="/register">
            @csrf
            <div>
                <label for="name">Full name</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="phone">Phone number</label>
                <input id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="language_preference">Preferred language</label>
                <select id="language_preference" name="language_preference">
                    <option value="en" @selected(old('language_preference') === 'en')>English</option>
                    <option value="sn" @selected(old('language_preference') === 'sn')>Shona</option>
                </select>
                @error('language_preference') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button type="submit">Create account</button>
        </form>

        <p>Already registered? <a href="/login">Log in</a>.</p>
    </section>
</x-layouts.public>
