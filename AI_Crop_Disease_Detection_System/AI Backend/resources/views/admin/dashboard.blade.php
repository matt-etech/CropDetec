<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - CropDetec</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <style>
        :root {
            --green: #14532d;
            --light: #f0fdf4;
            --line: #d7e5dc;
            --text: #111827;
            --muted: #52615a;
            --bg: #ffffff;
            --page-background:
                repeating-linear-gradient(0deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(17,24,39,.04) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(0deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(90deg, rgba(22,163,74,.025) 0 1px, transparent 1px 64px),
                #ffffff;
            --surface: #ffffff;
            --input: #ffffff;
        }
        :root[data-theme="dark"] {
            --green: #22c55e;
            --light: #052e16;
            --line: #1f2937;
            --text: #f8fafc;
            --muted: #cbd5e1;
            --bg: #000000;
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
        body { margin: 0; min-height: 100vh; color: var(--text); font-family: ui-sans-serif, system-ui, "Segoe UI", sans-serif; background: var(--page-background); background-attachment: fixed; }
        header { border-bottom: 1px solid var(--line); padding: 20px 28px; display: flex; justify-content: space-between; gap: 16px; }
        h1, h2, h3 { color: var(--green); margin: 0; }
        main { max-width: 1240px; margin: 0 auto; padding: 24px; }
        .grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
        .panel, .stat { border: 1px solid var(--line); border-radius: 8px; padding: 16px; background: var(--surface); }
        .stat strong { display: block; font-size: 30px; color: var(--green); }
        .stat span, p, td, th, label { color: var(--muted); }
        .columns { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px; }
        form { display: grid; gap: 10px; margin-top: 12px; }
        input, select, textarea { background: var(--input); border: 1px solid var(--line); border-radius: 8px; color: var(--text); font: inherit; padding: 10px; width: 100%; }
        button, .button { background: var(--green); border: 0; border-radius: 8px; color: #fff; cursor: pointer; font-weight: 800; padding: 10px 14px; }
        :root[data-theme="dark"] button, :root[data-theme="dark"] .button { color: #000; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid var(--line); padding: 10px; text-align: left; vertical-align: top; }
        .section { margin-top: 18px; }
        .badge { background: var(--light); border: 1px solid var(--line); border-radius: 999px; color: var(--green); display: inline-block; font-size: 12px; font-weight: 800; padding: 4px 8px; }
        .header-actions { align-items: center; display: flex; gap: 10px; }
        .theme-toggle { background: var(--surface); border: 1px solid var(--line); color: var(--green); }
        :root[data-theme="dark"] .theme-toggle { color: var(--green); }
        @media (max-width: 900px) { .grid, .columns { grid-template-columns: 1fr; } header { flex-direction: column; } }
    </style>
    <script>
        const savedTheme = localStorage.getItem('cropTheme') || 'light';
        document.documentElement.dataset.theme = savedTheme;
    </script>
</head>
<body>
    <header>
        <div>
            <h1>Admin Dashboard</h1>
            <p>Signed in with an administrator API token as {{ $admin->name }}.</p>
        </div>
        <div class="header-actions">
            <button class="theme-toggle" type="button" data-theme-toggle>Dark mode</button>
            <a class="button" href="/">Project Home</a>
        </div>
    </header>

    <main>
        <section class="grid" aria-label="Summary">
            @foreach ($stats as $label => $value)
                <div class="stat">
                    <strong>{{ $value }}</strong>
                    <span>{{ Illuminate\Support\Str::headline($label) }}</span>
                </div>
            @endforeach
        </section>

        <section class="columns section">
            <div class="panel">
                <h2>Add Crop</h2>
                <form method="post" action="/admin/crops">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <label for="crop-name">Crop name</label>
                    <input id="crop-name" name="name" required>
                    <label for="crop-name-sn">Crop name in Shona</label>
                    <input id="crop-name-sn" name="name_sn">
                    <label for="crop-scientific-name">Scientific name</label>
                    <input id="crop-scientific-name" name="scientific_name">
                    <label for="crop-description">Description</label>
                    <textarea id="crop-description" name="description"></textarea>
                    <label for="crop-description-sn">Description in Shona</label>
                    <textarea id="crop-description-sn" name="description_sn"></textarea>
                    <button type="submit">Save crop</button>
                </form>
            </div>

            <div class="panel">
                <h2>Add Disease</h2>
                <form method="post" action="/admin/diseases">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <label for="disease-crop">Crop</label>
                    <select id="disease-crop" name="crop_id" required>
                        @foreach ($crops as $crop)
                            <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                        @endforeach
                    </select>
                    <label for="disease-name">Disease name</label>
                    <input id="disease-name" name="name" required>
                    <label for="disease-name-sn">Disease name in Shona</label>
                    <input id="disease-name-sn" name="name_sn">
                    <label for="disease-class-label">AI class label</label>
                    <input id="disease-class-label" name="class_label" required>
                    <label for="disease-description">Description</label>
                    <textarea id="disease-description" name="description"></textarea>
                    <label for="disease-description-sn">Description in Shona</label>
                    <textarea id="disease-description-sn" name="description_sn"></textarea>
                    <label for="disease-symptoms">Symptoms</label>
                    <textarea id="disease-symptoms" name="symptoms"></textarea>
                    <label for="disease-symptoms-sn">Symptoms in Shona</label>
                    <textarea id="disease-symptoms-sn" name="symptoms_sn"></textarea>
                    <label for="disease-prevention">Prevention</label>
                    <textarea id="disease-prevention" name="prevention"></textarea>
                    <label for="disease-prevention-sn">Prevention in Shona</label>
                    <textarea id="disease-prevention-sn" name="prevention_sn"></textarea>
                    <button type="submit">Save disease</button>
                </form>
            </div>
        </section>

        <section class="panel section">
            <h2>Add Treatment</h2>
            <form method="post" action="/admin/treatments">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label for="treatment-disease">Disease</label>
                <select id="treatment-disease" name="disease_id" required>
                    @foreach ($diseases as $disease)
                        <option value="{{ $disease->id }}">{{ $disease->crop?->name }} - {{ $disease->name }}</option>
                    @endforeach
                </select>
                <label for="treatment-title">Treatment title</label>
                <input id="treatment-title" name="title" required>
                <label for="treatment-title-sn">Treatment title in Shona</label>
                <input id="treatment-title-sn" name="title_sn">
                <label for="treatment-type">Type, for example cultural or chemical</label>
                <input id="treatment-type" name="type">
                <label for="treatment-instructions">Instructions</label>
                <textarea id="treatment-instructions" name="instructions" required></textarea>
                <label for="treatment-instructions-sn">Instructions in Shona</label>
                <textarea id="treatment-instructions-sn" name="instructions_sn"></textarea>
                <button type="submit">Save treatment</button>
            </form>
        </section>

        <section class="panel section">
            <h2>Recent Diagnoses</h2>
            <table>
                <thead><tr><th>Farmer</th><th>Crop</th><th>Disease</th><th>Confidence</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($diagnoses as $diagnosis)
                        <tr>
                            <td>{{ $diagnosis->user?->name }}</td>
                            <td>{{ $diagnosis->crop?->name ?? 'Unknown' }}</td>
                            <td>{{ $diagnosis->disease?->name ?? $diagnosis->predicted_label }}</td>
                            <td>{{ $diagnosis->confidence }}%</td>
                            <td><span class="badge">{{ $diagnosis->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="panel section">
            <h2>Users</h2>
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Language</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->language_preference }}</td>
                            <td>
                                <form method="post" action="/admin/users/{{ $user->id }}/role">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <label for="role-{{ $user->id }}">Role for {{ $user->name }}</label>
                                    <select id="role-{{ $user->id }}" name="role">
                                        <option value="farmer" @selected($user->role === 'farmer')>Farmer</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
