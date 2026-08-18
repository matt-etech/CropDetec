<x-layouts.public title="{{ portal_text($user, 'diagnosis_history') }} - CropDetec" :user="$user">
    <section class="panel">
        <h1>{{ portal_text($user, 'diagnosis_history') }}</h1>
        <p>{{ portal_text($user, 'history_intro') }}</p>

        <form method="get" action="/diagnoses">
            <div>
                <label for="crop_id">{{ portal_text($user, 'crop') }}</label>
                <select id="crop_id" name="crop_id">
                    <option value="">{{ portal_text($user, 'all_crops') }}</option>
                    @foreach ($crops as $crop)
                        <option value="{{ $crop->id }}" @selected(($filters['crop_id'] ?? '') == $crop->id)>{{ $crop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="disease_id">{{ portal_text($user, 'disease') }}</label>
                <select id="disease_id" name="disease_id">
                    <option value="">{{ portal_text($user, 'all_diseases') }}</option>
                    @foreach ($diseases as $disease)
                        <option value="{{ $disease->id }}" @selected(($filters['disease_id'] ?? '') == $disease->id)>{{ $disease->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date">{{ portal_text($user, 'date') }}</label>
                <select id="date" name="date">
                    <option value="">{{ portal_text($user, 'all_dates') }}</option>
                    <option value="today" @selected(($filters['date'] ?? '') === 'today')>{{ portal_text($user, 'today') }}</option>
                    <option value="week" @selected(($filters['date'] ?? '') === 'week')>{{ portal_text($user, 'last_7_days') }}</option>
                </select>
            </div>
            <button type="submit">{{ portal_text($user, 'apply_filters') }}</button>
        </form>
    </section>

    <section class="panel" style="margin-top:18px">
        @if ($diagnoses->isEmpty())
            <p>{{ portal_text($user, 'no_matching_diagnoses') }}</p>
        @else
            <table>
                <thead><tr><th>{{ portal_text($user, 'date') }}</th><th>{{ portal_text($user, 'crop') }}</th><th>{{ portal_text($user, 'disease') }}</th><th>{{ portal_text($user, 'confidence') }}</th><th></th></tr></thead>
                <tbody>
                    @foreach ($diagnoses as $diagnosis)
                        <tr>
                            <td>{{ $diagnosis->created_at?->format('Y-m-d') }}</td>
                            <td>{{ $diagnosis->crop?->name ?? portal_text($user, 'unknown_crop') }}</td>
                            <td>{{ $diagnosis->disease?->name ?? $diagnosis->predicted_label }}</td>
                            <td>{{ $diagnosis->confidence }}%</td>
                            <td><a href="/diagnoses/{{ $diagnosis->id }}">{{ portal_text($user, 'open') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</x-layouts.public>
