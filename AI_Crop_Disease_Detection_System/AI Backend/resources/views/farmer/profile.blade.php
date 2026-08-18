<x-layouts.public title="{{ portal_text($user, 'profile') }} - CropDetec" :user="$user">
    <section class="auth-card">
        <h1>{{ portal_text($user, 'profile') }}</h1>
        <p>{{ portal_text($user, 'profile_intro') }}</p>

        @if (session('status_key'))
            <p style="color:var(--green-800); font-weight:800">{{ portal_text($user, session('status_key')) }}</p>
        @endif

        <form method="post" action="/profile">
            @csrf
            <div>
                <label for="name">{{ portal_text($user, 'full_name') }}</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label>{{ portal_text($user, 'email_address') }}</label>
                <input value="{{ $user->email }}" disabled>
            </div>
            <div>
                <label for="phone">{{ portal_text($user, 'phone_number') }}</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="language_preference">{{ portal_text($user, 'preferred_language') }}</label>
                <select id="language_preference" name="language_preference">
                    <option value="en" @selected(old('language_preference', $user->language_preference) === 'en')>{{ portal_text($user, 'english') }}</option>
                    <option value="sn" @selected(old('language_preference', $user->language_preference) === 'sn')>{{ portal_text($user, 'shona') }}</option>
                </select>
                @error('language_preference') <div class="error">{{ $message }}</div> @enderror
            </div>
            <button type="submit">{{ portal_text($user, 'save_profile') }}</button>
        </form>
    </section>
</x-layouts.public>
