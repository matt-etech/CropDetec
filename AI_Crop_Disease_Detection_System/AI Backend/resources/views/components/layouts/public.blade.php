@php
    $portalUser = $user ?? null;
    $portalLanguage = $portalUser?->language_preference ?? 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ $portalLanguage }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CropDetec' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --green-900: #0b2f1d;
            --green-800: #14532d;
            --green-700: #15803d;
            --green-50: #f0fdf4;
            --line: #d7e5dc;
            --muted: #52615a;
            --white: #ffffff;
            --text: #111827;
            --bg-start: var(--green-50);
            --bg-end: var(--white);
            --page-background:
                repeating-linear-gradient(0deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(0deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(90deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                linear-gradient(180deg, var(--bg-start), var(--bg-end) 28rem);
            --surface: var(--white);
            --input: var(--white);
        }
        :root[data-theme="dark"] {
            --green-900: #f8fafc;
            --green-800: #22c55e;
            --green-700: #4ade80;
            --green-50: #052e16;
            --line: #1f2937;
            --muted: #cbd5e1;
            --white: #0b1220;
            --text: #f8fafc;
            --bg-start: #000000;
            --bg-end: #000000;
            --page-background:
                repeating-linear-gradient(0deg, rgba(255,255,255,.045) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(255,255,255,.045) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(0deg, rgba(34,197,94,.025) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(90deg, rgba(34,197,94,.025) 0 1px, transparent 1px 64px),
                #000000;
            --surface: #0b1220;
            --input: #111827;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--page-background);
            background-attachment: fixed;
            color: var(--text);
            font-family: ui-sans-serif, system-ui, "Segoe UI", sans-serif;
        }
        a { color: var(--green-800); font-weight: 800; text-decoration: none; }
        .topbar {
            align-items: center;
            border-bottom: 1px solid var(--line);
            background: var(--bg-end);
            display: flex;
            justify-content: space-between;
            padding: 18px 28px;
        }
        .brand { color: var(--green-900); font-size: 18px; font-weight: 900; }
        .nav { display: flex; gap: 12px; }
        .skip-link {
            background: var(--green-800);
            color: white;
            left: 12px;
            padding: 10px 12px;
            position: absolute;
            top: -100px;
            z-index: 10;
        }
        .skip-link:focus { top: 12px; }
        main { margin: 0 auto; max-width: 980px; padding: 42px 20px; }
        .auth-card, .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 24px;
        }
        .auth-card { margin: 0 auto; max-width: 520px; }
        h1 { color: var(--green-900); font-size: 42px; line-height: 1.05; margin: 0 0 10px; }
        h2 { color: var(--green-900); margin: 0 0 12px; }
        p { color: var(--muted); line-height: 1.6; }
        form { display: grid; gap: 14px; margin-top: 18px; }
        label { color: var(--green-900); font-weight: 800; }
        input, select {
            background: var(--input);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            font: inherit;
            min-height: 46px;
            padding: 10px 12px;
            width: 100%;
        }
        input[type="file"] { padding-top: 12px; }
        button, .button {
            align-items: center;
            background: var(--green-800);
            border: 0;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 900;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
        }
        .button.secondary {
            background: var(--surface);
            border: 1px solid var(--green-800);
            color: var(--green-800);
        }
        .theme-toggle {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--green-800);
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            min-height: 40px;
            padding: 0 12px;
        }
        .error {
            color: #9f1239;
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
        }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border-bottom: 1px solid var(--line); color: var(--muted); padding: 12px; text-align: left; }
        th { color: var(--green-900); }
        @media (max-width: 720px) {
            .topbar { align-items: flex-start; flex-direction: column; gap: 12px; }
            h1 { font-size: 34px; }
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('cropTheme') || 'light';
        document.documentElement.dataset.theme = savedTheme;
    </script>
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <header class="topbar">
        <a class="brand" href="/">{{ portal_text($portalLanguage, 'brand') }}</a>
        <nav class="nav">
            @if ($portalUser)
                <a href="/dashboard">{{ portal_text($portalLanguage, 'dashboard') }}</a>
                <a href="/diagnose">{{ portal_text($portalLanguage, 'upload') }}</a>
                <a href="/diagnoses">{{ portal_text($portalLanguage, 'history') }}</a>
                <a href="/crops">{{ portal_text($portalLanguage, 'library') }}</a>
                <a href="/profile">{{ portal_text($portalLanguage, 'profile') }}</a>
            @else
                <a href="/register">Sign up</a>
                <a href="/login">Log in</a>
            @endif
            <button class="theme-toggle" type="button" data-theme-toggle>Dark mode</button>
        </nav>
    </header>
    <main id="main-content">
        {{ $slot }}
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
