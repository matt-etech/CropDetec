<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CropDetec</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --green-900: #0b2f1d;
            --green-800: #14532d;
            --green-700: #15803d;
            --green-100: #dcfce7;
            --green-50: #f0fdf4;
            --black: #111827;
            --muted: #52615a;
            --line: #d7e5dc;
            --white: #ffffff;
            --bg-start: var(--green-50);
            --bg-end: var(--white);
            --page-background:
                repeating-linear-gradient(0deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(0deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(90deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                linear-gradient(180deg, var(--bg-start), var(--bg-end) 34rem);
            --surface: var(--white);
        }
        :root[data-theme="dark"] {
            --green-900: #f8fafc;
            --green-800: #22c55e;
            --green-700: #4ade80;
            --green-100: #052e16;
            --green-50: #000000;
            --black: #f8fafc;
            --muted: #cbd5e1;
            --line: #1f2937;
            --white: #0b1220;
            --bg-start: #000000;
            --bg-end: #000000;
            --page-background:
                repeating-linear-gradient(0deg, rgba(255,255,255,.045) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(255,255,255,.045) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(0deg, rgba(34,197,94,.025) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(90deg, rgba(34,197,94,.025) 0 1px, transparent 1px 64px),
                #000000;
            --surface: #0b1220;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--black);
            background: var(--page-background);
            background-attachment: fixed;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        .skip-link {
            background: var(--green-900);
            border-radius: 0 0 8px 0;
            color: var(--white);
            font-weight: 800;
            left: 0;
            padding: 10px 14px;
            position: absolute;
            top: -48px;
            z-index: 20;
        }
        .skip-link:focus { top: 0; }
        .topbar {
            align-items: center;
            border-bottom: 1px solid var(--line);
            background: var(--bg-end);
            display: flex;
            justify-content: space-between;
            min-height: 76px;
            padding: 0 32px;
        }
        .brand {
            align-items: center;
            color: var(--green-900);
            display: inline-flex;
            font-size: 18px;
            font-weight: 800;
            gap: 12px;
        }
        .brand-mark {
            align-items: center;
            background: var(--black);
            border-radius: 8px;
            color: var(--white);
            display: inline-flex;
            height: 40px;
            justify-content: center;
            overflow: hidden;
            width: 40px;
        }
        .brand-mark img {
            display: block;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }
        .nav-links { align-items: center; display: flex; gap: 8px; }
        .nav-links a {
            border-radius: 8px;
            color: var(--green-900);
            font-size: 14px;
            font-weight: 700;
            padding: 10px 12px;
        }
        .nav-links a:hover { background: var(--green-100); }
        .theme-toggle {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--green-800);
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            min-height: 40px;
            padding: 0 12px;
        }
        .dashboard {
            margin: 0 auto;
            max-width: 1180px;
            padding: 48px 24px 72px;
        }
        .hero {
            align-items: stretch;
            display: grid;
            gap: 28px;
            grid-template-columns: minmax(0, 1fr) 360px;
        }
        .eyebrow {
            color: var(--green-700);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
            margin: 0 0 12px;
            text-transform: uppercase;
        }
        .hero h1 {
            color: var(--green-900);
            font-size: clamp(36px, 6vw, 68px);
            letter-spacing: 0;
            line-height: 1;
            margin: 0;
            max-width: 820px;
        }
        .hero-copy {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.7;
            margin: 22px 0 0;
            max-width: 720px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }
        .button {
            align-items: center;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 800;
            min-height: 46px;
            padding: 0 18px;
        }
        .button-primary { background: var(--green-800); color: var(--white); }
        .button-secondary {
            background: var(--surface);
            border: 1px solid var(--green-800);
            color: var(--green-900);
        }
        .status-panel {
            align-self: end;
            background: var(--green-900);
            border-radius: 8px;
            color: var(--white);
            padding: 18px;
        }
        .status-row {
            align-items: center;
            border-bottom: 1px solid rgb(255 255 255 / 16%);
            display: flex;
            justify-content: space-between;
            min-height: 54px;
        }
        .status-row:last-child { border-bottom: 0; }
        .status-row span { color: var(--green-100); }
        .project-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-top: 24px;
            padding: 24px;
        }
        .feature-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 22px;
        }
        .feature-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
        }
        .feature-card h3 {
            color: var(--green-900);
            font-size: 20px;
            line-height: 1.2;
            margin: 0 0 8px;
        }
        .feature-card p {
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
        }
        .work-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-top: 44px;
        }
        .work-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            min-height: 220px;
            padding: 22px;
        }
        .work-card.active {
            border-color: var(--green-700);
            box-shadow: 0 14px 36px rgb(20 83 45 / 14%);
        }
        .phase-label {
            color: var(--green-700);
            font-size: 13px;
            font-weight: 900;
        }
        .work-card h2 {
            color: var(--green-900);
            font-size: 22px;
            line-height: 1.2;
            margin: 14px 0 10px;
        }
        .work-card p {
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
        }
        .backend-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-top: 24px;
            padding: 24px;
        }
        .section-title {
            color: var(--green-900);
            font-size: 28px;
            line-height: 1.2;
            margin: 0 0 8px;
        }
        .section-copy {
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
            max-width: 760px;
        }
        .stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            margin-top: 20px;
        }
        .stat {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
        }
        .stat strong {
            color: var(--green-900);
            display: block;
            font-size: 28px;
            line-height: 1;
        }
        .stat span {
            color: var(--muted);
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-top: 8px;
        }
        .backend-columns {
            display: grid;
            gap: 16px;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
        }
        .backend-list {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
        }
        .backend-list h3 {
            color: var(--green-900);
            font-size: 18px;
            margin: 0 0 12px;
        }
        .backend-list ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .backend-list li {
            align-items: flex-start;
            color: var(--black);
            display: flex;
            gap: 10px;
            line-height: 1.5;
            padding: 7px 0;
        }
        .backend-list li::before {
            background: var(--green-700);
            border-radius: 999px;
            content: "";
            flex: 0 0 auto;
            height: 8px;
            margin-top: 8px;
            width: 8px;
        }
        code {
            background: var(--green-50);
            border: 1px solid var(--line);
            border-radius: 6px;
            color: var(--green-900);
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 13px;
            padding: 2px 6px;
        }
        @media (max-width: 900px) {
            .topbar, .nav-links {
                align-items: flex-start;
                flex-direction: column;
            }
            .topbar { gap: 14px; padding: 20px; }
            .hero, .feature-grid, .work-grid, .stats-grid, .backend-columns { grid-template-columns: 1fr; }
            .status-panel { align-self: stretch; }
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('cropTheme') || 'light';
        document.documentElement.dataset.theme = savedTheme;
    </script>
</head>
<body class="app-shell">
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <header class="topbar">
        <a class="brand" href="/">
            <span class="brand-mark"><img src="{{ asset('cropdetec-icon.png') }}" alt="" width="40" height="40"></span>
            <span>CropDetec</span>
        </a>
        <nav class="nav-links" aria-label="Primary">
            <a href="{{ url('/register') }}">Sign up</a>
            <a href="{{ url('/login') }}">Log in</a>
            <a href="{{ url('/dashboard') }}">Dashboard</a>
            <button class="theme-toggle" type="button" data-theme-toggle>Dark mode</button>
        </nav>
    </header>

    <main id="main-content" class="dashboard">
        <section class="hero">
            <div>
                <p class="eyebrow">AI-assisted crop care</p>
                <h1>Check crop leaf symptoms and keep your farm records in one place.</h1>
                <p class="hero-copy">
                    This project helps farmers capture crop leaf photos, receive disease guidance,
                    review previous diagnoses, and keep practical treatment advice close at hand.
                </p>
                <div class="hero-actions">
                    <a class="button button-primary" href="{{ url('/register') }}">Create farmer account</a>
                    <a class="button button-secondary" href="{{ url('/login') }}">Log in</a>
                </div>
            </div>
            <div class="status-panel" aria-label="Current build status">
                <div class="status-row">
                    <span>Supported crops</span>
                    <strong>{{ $stats['crops'] }}</strong>
                </div>
                <div class="status-row">
                    <span>Disease guides</span>
                    <strong>{{ $stats['diseases'] }}</strong>
                </div>
                <div class="status-row">
                    <span>Saved diagnoses</span>
                    <strong>{{ $stats['diagnoses'] }}</strong>
                </div>
            </div>
        </section>

        <section class="project-panel" aria-label="How the project helps farmers">
            <h2 class="section-title">Built for quick, practical crop support</h2>
            <p class="section-copy">
                The system combines a farmer account, diagnosis history, crop and disease records,
                and AI-assisted prediction so farmers can act earlier when leaves begin to show symptoms.
            </p>

            <div class="feature-grid">
                <article class="feature-card">
                    <h3>Capture symptoms</h3>
                    <p>Use the mobile workflow to take or choose a clear crop leaf photo for analysis.</p>
                </article>
                <article class="feature-card">
                    <h3>Understand the result</h3>
                    <p>See the predicted disease, confidence level, symptoms, prevention notes, and treatment guidance.</p>
                </article>
                <article class="feature-card">
                    <h3>Track your history</h3>
                    <p>Keep previous diagnoses connected to your farmer profile so you can review changes over time.</p>
                </article>
            </div>
        </section>

        <section class="work-grid" aria-label="Development phases">
            <article class="work-card active">
                <span class="phase-label">01</span>
                <h2>Farmer Accounts</h2>
                <p>Create a profile, choose your language preference, and keep your records private.</p>
            </article>
            <article class="work-card">
                <span class="phase-label">02</span>
                <h2>Crop Diagnosis</h2>
                <p>Upload leaf images and receive AI-assisted disease guidance for supported crops.</p>
            </article>
            <article class="work-card">
                <span class="phase-label">03</span>
                <h2>Treatment Guidance</h2>
                <p>Read practical recommendations, prevention steps, and low-confidence warnings.</p>
            </article>
            <article class="work-card">
                <span class="phase-label">04</span>
                <h2>Decision Support</h2>
                <p>Use results as a helpful guide, then confirm serious cases with an agricultural professional.</p>
            </article>
        </section>
    </main>
    <script>
        const themeToggle = document.querySelector('[data-theme-toggle]');
        const setTheme = (theme) => {
            document.documentElement.dataset.theme = theme;
            localStorage.setItem('cropTheme', theme);
            if (themeToggle) {
                themeToggle.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
            }
        };
        setTheme(localStorage.getItem('cropTheme') || 'light');
        themeToggle?.addEventListener('click', () => {
            setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
        });
    </script>
</body>
</html>
