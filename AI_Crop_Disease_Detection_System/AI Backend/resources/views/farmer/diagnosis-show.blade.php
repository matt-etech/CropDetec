<x-layouts.public title="{{ portal_text($user, 'recommendation') }} - CropDetec" :user="$user">
    @php
        $useShona = $user->language_preference === 'sn';
        $cropName = $useShona && $diagnosis->crop?->name_sn ? $diagnosis->crop->name_sn : $diagnosis->crop?->name;
        $diseaseName = $useShona && $diagnosis->disease?->name_sn ? $diagnosis->disease->name_sn : $diagnosis->disease?->name;
    @endphp
    <section class="panel">
        <h1>{{ $diseaseName ?? $diagnosis->predicted_label }}</h1>
        <p>{{ $cropName ?? portal_text($user, 'unknown_crop') }} - {{ portal_text($user, 'confidence') }}: {{ $diagnosis->confidence }}%</p>
        <div class="actions">
            <a class="button" href="/diagnose">{{ portal_text($user, 'upload_another') }}</a>
            <a class="button secondary" href="/diagnoses">{{ portal_text($user, 'back_to_history') }}</a>
        </div>
    </section>

    <section class="panel" style="margin-top:18px">
        <h2>{{ portal_text($user, 'uploaded_image') }}</h2>
        <img src="{{ $imageUrl }}" alt="Uploaded crop leaf" style="border-radius:8px; max-height:360px; max-width:100%; object-fit:cover;">
    </section>

    <section class="panel" style="margin-top:18px">
        <h2>{{ portal_text($user, 'recommendation') }}</h2>
        <p style="white-space:pre-line">{{ $diagnosis->recommendation_snapshot }}</p>
        @if ($diagnosis->confidence < 60)
            <p><strong>{{ portal_text($user, 'low_confidence') }}:</strong> {{ portal_text($user, 'low_confidence_advice') }}</p>
        @endif
    </section>
</x-layouts.public>
