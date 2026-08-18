<x-layouts.public title="{{ portal_text($user, 'dashboard') }} - CropDetec" :user="$user">
    <style>
        #main-content {
            max-width: 1480px;
            padding-top: 28px;
        }
        .dashboard-action-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            margin-top: 18px;
        }
        .dashboard-greeting {
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 25vh;
        }
        .dashboard-divider {
            border: 0;
            border-top: 1px solid var(--line);
            margin: 24px 0 28px;
        }
        .dashboard-section-kicker {
            color: var(--muted);
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .18em;
            margin: 0 0 12px;
            text-transform: uppercase;
        }
        .dashboard-section-title {
            color: var(--green-900);
            font-size: 30px;
            line-height: 1.15;
            margin: 0;
        }
        .dashboard-action-card {
            align-items: stretch;
            appearance: none;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
            color: inherit;
            display: flex;
            flex-direction: column;
            font-weight: 400;
            gap: 14px;
            justify-content: flex-start;
            min-height: 194px;
            padding: 22px 20px;
            text-decoration: none;
            transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
        }
        :root[data-theme="dark"] .dashboard-action-card {
            box-shadow: 0 8px 22px rgba(0, 0, 0, .28);
        }
        .dashboard-action-card:hover,
        .dashboard-action-card:focus {
            border-color: var(--green-800);
            box-shadow: 0 12px 26px rgba(15, 23, 42, .12);
            outline: 0;
            transform: translateY(-2px);
        }
        .dashboard-action-card strong {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.2;
        }
        .dashboard-action-card p {
            margin: 0;
        }
        .dashboard-action-icon {
            color: var(--green-800);
            display: block;
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 0;
            line-height: 1;
            margin-top: 12px;
        }
        .dashboard-action-cta {
            color: var(--green-800);
            font-weight: 900;
            margin-top: auto;
        }
        .dashboard-logout-card {
            cursor: pointer;
            font: inherit;
            text-align: left;
            width: 100%;
        }
        .dashboard-logout-card .dashboard-action-cta {
            color: var(--green-800);
        }
        @media (max-width: 720px) {
            #main-content {
                padding-top: 20px;
            }
            .dashboard-section-title {
                font-size: 26px;
            }
        }
    </style>

    <section class="panel dashboard-greeting">
        <h1>{{ str_replace(':name', $user->name, portal_text($user, 'hello')) }}</h1>
        <p>{{ portal_text($user, 'dashboard_intro') }}</p>
    </section>

    <hr class="dashboard-divider">

    <section aria-labelledby="dashboard-actions-title">
        <p class="dashboard-section-kicker">{{ portal_text($user, 'workspace_shortcuts') }}</p>
        <h2 class="dashboard-section-title" id="dashboard-actions-title">{{ portal_text($user, 'operational_areas') }}</h2>
        <div class="dashboard-action-grid">
            <a class="dashboard-action-card" href="/diagnose">
                <strong>{{ portal_text($user, 'upload_image') }}</strong>
                <span class="dashboard-action-icon" aria-hidden="true">1</span>
                <p>{{ portal_text($user, 'upload_image_hint') }}</p>
                <span class="dashboard-action-cta">{{ portal_text($user, 'open') }}</span>
            </a>
            <a class="dashboard-action-card" href="/diagnoses">
                <strong>{{ portal_text($user, 'view_history') }}</strong>
                <span class="dashboard-action-icon" aria-hidden="true">2</span>
                <p>{{ portal_text($user, 'view_history_hint') }}</p>
                <span class="dashboard-action-cta">{{ portal_text($user, 'open') }}</span>
            </a>
            <a class="dashboard-action-card" href="/crops">
                <strong>{{ portal_text($user, 'crop_library') }}</strong>
                <span class="dashboard-action-icon" aria-hidden="true">3</span>
                <p>{{ portal_text($user, 'crop_library_hint') }}</p>
                <span class="dashboard-action-cta">{{ portal_text($user, 'open') }}</span>
            </a>
            <a class="dashboard-action-card" href="/profile">
                <strong>{{ portal_text($user, 'profile') }}</strong>
                <span class="dashboard-action-icon" aria-hidden="true">4</span>
                <p>{{ portal_text($user, 'profile_hint') }}</p>
                <span class="dashboard-action-cta">{{ portal_text($user, 'open') }}</span>
            </a>
            <form method="post" action="/logout" style="margin:0">
                @csrf
                <button class="dashboard-action-card dashboard-logout-card" type="submit">
                    <strong>{{ portal_text($user, 'logout') }}</strong>
                    <span class="dashboard-action-icon" aria-hidden="true">5</span>
                    <p>{{ portal_text($user, 'logout_hint') }}</p>
                    <span class="dashboard-action-cta">{{ portal_text($user, 'logout') }}</span>
                </button>
            </form>
        </div>
    </section>
</x-layouts.public>
