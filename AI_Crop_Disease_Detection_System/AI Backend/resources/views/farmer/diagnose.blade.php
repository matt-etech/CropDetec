<x-layouts.public title="{{ portal_text($user, 'upload_crop_image') }} - CropDetec" :user="$user">
    <section class="auth-card">
        <h1>{{ portal_text($user, 'upload_crop_image') }}</h1>
        <p>{{ portal_text($user, 'upload_intro') }}</p>

        <form method="post" action="/diagnose" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="crop_id">{{ portal_text($user, 'crop') }}</label>
                <select id="crop_id" name="crop_id">
                    <option value="">{{ portal_text($user, 'first_matching_crop') }}</option>
                    @foreach ($crops as $crop)
                        <option value="{{ $crop->id }}" @selected(old('crop_id') == $crop->id)>{{ $crop->name }}</option>
                    @endforeach
                </select>
                @error('crop_id') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="image">{{ portal_text($user, 'leaf_image') }}</label>
                <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" required>
                @error('image') <div class="error">{{ $message }}</div> @enderror
            </div>
            <button type="submit">{{ portal_text($user, 'run_diagnosis') }}</button>
        </form>

        <p>{{ portal_text($user, 'disclaimer') }}</p>
    </section>
</x-layouts.public>
