<x-layouts.public title="{{ portal_text($user, 'crop_library') }} - CropDetec" :user="$user">
    <style>
        .crop-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin-top: 18px;
        }
        .crop-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .crop-image {
            background:
                repeating-linear-gradient(0deg, rgba(17,24,39,.055) 0 1px, transparent 1px 28px),
                repeating-linear-gradient(90deg, rgba(17,24,39,.055) 0 1px, transparent 1px 28px),
                linear-gradient(135deg, color-mix(in srgb, var(--crop-secondary) 20%, var(--surface)), color-mix(in srgb, var(--crop-primary) 22%, var(--surface)));
            border-bottom: 1px solid var(--line);
            min-height: 172px;
            position: relative;
        }
        :root[data-theme="dark"] .crop-image {
            background:
                repeating-linear-gradient(0deg, rgba(255,255,255,.045) 0 1px, transparent 1px 28px),
                repeating-linear-gradient(90deg, rgba(255,255,255,.045) 0 1px, transparent 1px 28px),
                linear-gradient(135deg, color-mix(in srgb, var(--crop-secondary) 20%, var(--surface)), color-mix(in srgb, var(--crop-primary) 22%, var(--surface)));
        }
        .crop-image svg {
            display: block;
            height: 172px;
            width: 100%;
        }
        .crop-badge {
            background: color-mix(in srgb, var(--surface) 86%, transparent);
            border: 1px solid var(--line);
            border-radius: 8px;
            bottom: 12px;
            color: var(--green-800);
            font-size: 12px;
            font-weight: 900;
            left: 12px;
            padding: 6px 10px;
            position: absolute;
        }
        .crop-card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 18px;
        }
        .crop-card-body h2 { margin-bottom: 6px; }
        .crop-scientific {
            color: var(--green-800);
            font-weight: 800;
            margin: 0 0 10px;
        }
        .disease-list {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }
        .disease-card {
            border-top: 1px solid var(--line);
            padding-top: 14px;
        }
        .disease-card h3 {
            color: var(--green-900);
            margin: 0 0 8px;
        }
        .treatment-note {
            background: var(--green-50);
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-top: 10px;
            padding: 10px 12px;
        }
        :root[data-theme="dark"] .treatment-note {
            background: #052e16;
        }
    </style>

    <section class="panel">
        <h1>{{ portal_text($user, 'crop_library') }}</h1>
        <p>{{ portal_text($user, 'crop_library_intro') }}</p>
    </section>

    <section class="crop-grid" aria-label="{{ portal_text($user, 'crop_library') }}">
        @foreach ($crops as $crop)
            @php
                $useShona = $user->language_preference === 'sn';
                $cropName = $useShona && $crop->name_sn ? $crop->name_sn : $crop->name;
                $cropDescription = $useShona && $crop->description_sn ? $crop->description_sn : $crop->description;
                $normalizedName = Str::lower($crop->name);
                $visual = match (true) {
                    Str::contains($normalizedName, ['maize', 'corn']) => ['type' => 'maize', 'label' => 'Cereal crop', 'primary' => '#facc15', 'secondary' => '#15803d'],
                    Str::contains($normalizedName, 'tomato') => ['type' => 'tomato', 'label' => 'Fruit vegetable', 'primary' => '#dc2626', 'secondary' => '#16a34a'],
                    Str::contains($normalizedName, 'potato') => ['type' => 'potato', 'label' => 'Root crop', 'primary' => '#d6a15e', 'secondary' => '#4d7c0f'],
                    Str::contains($normalizedName, 'pepper') => ['type' => 'pepper', 'label' => 'Vegetable crop', 'primary' => '#ef4444', 'secondary' => '#15803d'],
                    Str::contains($normalizedName, 'bean') => ['type' => 'bean', 'label' => 'Legume crop', 'primary' => '#a16207', 'secondary' => '#16a34a'],
                    Str::contains($normalizedName, 'soy') => ['type' => 'bean', 'label' => 'Oilseed crop', 'primary' => '#84cc16', 'secondary' => '#15803d'],
                    Str::contains($normalizedName, ['squash', 'pumpkin', 'cucumber']) => ['type' => 'squash', 'label' => 'Cucurbit crop', 'primary' => '#f97316', 'secondary' => '#16a34a'],
                    default => ['type' => 'leaf', 'label' => 'Supported crop', 'primary' => '#22c55e', 'secondary' => '#15803d'],
                };
            @endphp

            <article class="crop-card">
                <div class="crop-image" style="--crop-primary: {{ $visual['primary'] }}; --crop-secondary: {{ $visual['secondary'] }};">
                    <svg role="img" aria-label="{{ $cropName }} crop image" viewBox="0 0 320 172" preserveAspectRatio="xMidYMid slice">
                        <defs>
                            <filter id="soft-shadow-{{ $crop->id }}" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="8" stdDeviation="7" flood-color="#000000" flood-opacity=".18"/>
                            </filter>
                        </defs>

                        @if ($visual['type'] === 'maize')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <line x1="160" y1="142" x2="160" y2="44" stroke="{{ $visual['secondary'] }}" stroke-width="9" stroke-linecap="round"/>
                                <ellipse cx="126" cy="91" rx="22" ry="52" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-42 126 91)"/>
                                <ellipse cx="194" cy="89" rx="22" ry="52" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(42 194 89)"/>
                                <rect x="139" y="42" width="42" height="90" rx="22" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="143" cy="90" rx="15" ry="46" fill="{{ $visual['secondary'] }}" opacity=".85"/>
                                <ellipse cx="177" cy="90" rx="15" ry="46" fill="{{ $visual['secondary'] }}" opacity=".85"/>
                            </g>
                        @elseif ($visual['type'] === 'tomato')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <line x1="160" y1="132" x2="160" y2="48" stroke="{{ $visual['secondary'] }}" stroke-width="8" stroke-linecap="round"/>
                                <ellipse cx="132" cy="72" rx="18" ry="38" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-46 132 72)"/>
                                <ellipse cx="190" cy="72" rx="18" ry="38" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(46 190 72)"/>
                                <circle cx="137" cy="111" r="27" fill="{{ $visual['primary'] }}"/>
                                <circle cx="183" cy="106" r="28" fill="{{ $visual['primary'] }}"/>
                                <circle cx="162" cy="132" r="26" fill="{{ $visual['primary'] }}"/>
                                <circle cx="128" cy="101" r="6" fill="#ffffff" opacity=".28"/>
                            </g>
                        @elseif ($visual['type'] === 'potato')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <ellipse cx="136" cy="73" rx="20" ry="42" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-42 136 73)"/>
                                <ellipse cx="184" cy="69" rx="20" ry="42" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(42 184 69)"/>
                                <rect x="92" y="118" width="136" height="16" rx="8" fill="currentColor" opacity=".12"/>
                                <ellipse cx="132" cy="125" rx="34" ry="22" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="170" cy="132" rx="36" ry="22" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="204" cy="122" rx="32" ry="21" fill="{{ $visual['primary'] }}"/>
                            </g>
                        @elseif ($visual['type'] === 'pepper')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <line x1="160" y1="132" x2="160" y2="48" stroke="{{ $visual['secondary'] }}" stroke-width="8" stroke-linecap="round"/>
                                <ellipse cx="134" cy="70" rx="18" ry="40" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-48 134 70)"/>
                                <ellipse cx="188" cy="70" rx="18" ry="40" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(48 188 70)"/>
                                <path d="M157 76 C218 70 206 146 164 147 C119 146 105 79 157 76 Z" fill="{{ $visual['primary'] }}"/>
                                <circle cx="145" cy="100" r="8" fill="#ffffff" opacity=".25"/>
                            </g>
                        @elseif ($visual['type'] === 'bean')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <line x1="160" y1="142" x2="160" y2="48" stroke="{{ $visual['secondary'] }}" stroke-width="8" stroke-linecap="round"/>
                                <ellipse cx="126" cy="77" rx="20" ry="44" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-42 126 77)"/>
                                <ellipse cx="194" cy="77" rx="20" ry="44" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(42 194 77)"/>
                                <ellipse cx="126" cy="126" rx="17" ry="26" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="160" cy="108" rx="17" ry="26" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="194" cy="126" rx="17" ry="26" fill="{{ $visual['primary'] }}"/>
                            </g>
                        @elseif ($visual['type'] === 'squash')
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <ellipse cx="134" cy="66" rx="24" ry="48" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-42 134 66)"/>
                                <ellipse cx="188" cy="66" rx="24" ry="48" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(42 188 66)"/>
                                <ellipse cx="160" cy="121" rx="58" ry="39" fill="{{ $visual['primary'] }}"/>
                                <ellipse cx="136" cy="121" rx="20" ry="39" fill="{{ $visual['primary'] }}" opacity=".75"/>
                                <ellipse cx="184" cy="121" rx="20" ry="39" fill="{{ $visual['primary'] }}" opacity=".75"/>
                            </g>
                        @else
                            <g filter="url(#soft-shadow-{{ $crop->id }})">
                                <line x1="160" y1="142" x2="160" y2="46" stroke="{{ $visual['secondary'] }}" stroke-width="8" stroke-linecap="round"/>
                                <ellipse cx="130" cy="86" rx="28" ry="54" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(-38 130 86)"/>
                                <ellipse cx="190" cy="86" rx="28" ry="54" fill="{{ $visual['secondary'] }}" opacity=".9" transform="rotate(38 190 86)"/>
                            </g>
                        @endif
                    </svg>
                    <span class="crop-badge">{{ $visual['label'] }}</span>
                </div>

                <div class="crop-card-body">
                    <h2>{{ $cropName }}</h2>
                    @if ($crop->scientific_name)
                        <p class="crop-scientific">{{ $crop->scientific_name }}</p>
                    @endif
                    <p>{{ $cropDescription }}</p>

                    <div class="disease-list">
                        @foreach ($crop->diseases as $disease)
                            @php
                                $diseaseName = $useShona && $disease->name_sn ? $disease->name_sn : $disease->name;
                                $symptoms = $useShona && $disease->symptoms_sn ? $disease->symptoms_sn : $disease->symptoms;
                                $prevention = $useShona && $disease->prevention_sn ? $disease->prevention_sn : $disease->prevention;
                            @endphp
                            <div class="disease-card">
                                <h3>{{ $diseaseName }}</h3>
                                <p><strong>{{ portal_text($user, 'symptoms') }}:</strong> {{ $symptoms }}</p>
                                <p><strong>{{ portal_text($user, 'prevention') }}:</strong> {{ $prevention }}</p>
                                @foreach ($disease->treatments as $treatment)
                                    @php
                                        $treatmentTitle = $useShona && $treatment->title_sn ? $treatment->title_sn : $treatment->title;
                                        $treatmentInstructions = $useShona && $treatment->instructions_sn ? $treatment->instructions_sn : $treatment->instructions;
                                    @endphp
                                    <p class="treatment-note"><strong>{{ $treatmentTitle }}:</strong> {{ $treatmentInstructions }}</p>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        @endforeach
    </section>
</x-layouts.public>
