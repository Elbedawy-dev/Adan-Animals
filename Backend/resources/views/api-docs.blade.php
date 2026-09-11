@php
    $base = rtrim(url('/api'), '/');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ADAN REST API reference — authentication, locations, animals, reports, vaccines, and notifications.">

    <title>API documentation — ADAN</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-neutral-50 text-neutral-900 antialiased">
    <header class="border-b border-neutral-200 bg-white">
        <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-4 px-6 py-4 lg:px-8">
            <a href="{{ url('/') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-900">← Back to home</a>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">Staff login</a>
            @endif
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-6 py-12 lg:px-8 lg:py-16">
        <h1 class="text-3xl font-bold tracking-tight text-neutral-900">REST API reference</h1>
        <p class="mt-3 text-lg text-neutral-600">
            ADAN exposes a JSON API under a single base path. Authenticated routes use a Bearer token from Laravel Sanctum (<code class="rounded bg-neutral-200/80 px-1.5 py-0.5 text-sm">Authorization: Bearer {token}</code>).
        </p>

        <div class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-950">
            <p class="font-medium">Base URL</p>
            <code class="mt-1 block break-all text-base font-semibold">{{ $base }}</code>
            <p class="mt-3 text-emerald-900/90">
                Quick check:
                <a href="{{ url('/api/health') }}" class="font-medium underline underline-offset-2 hover:no-underline">GET {{ $base }}/health</a>
                (returns JSON status)
            </p>
        </div>

        <nav class="mt-10 flex flex-wrap gap-2 text-sm">
            <a href="#overview" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Overview</a>
            <a href="#auth" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Auth</a>
            <a href="#locations" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Locations</a>
            <a href="#animals" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Animals</a>
            <a href="#my-animals" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">My animals</a>
            <a href="#vaccines" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Vaccines</a>
            <a href="#reports" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Reports</a>
            <a href="#notifications" class="rounded-full bg-neutral-200/80 px-3 py-1 text-neutral-800 hover:bg-neutral-300/80">Notifications</a>
        </nav>

        <section id="overview" class="mt-14 scroll-mt-20">
            <h2 class="text-xl font-bold text-neutral-900">Overview</h2>
            <ul class="mt-4 list-disc space-y-2 pl-5 text-neutral-600">
                <li>Request and response bodies are JSON unless noted.</li>
                <li>Endpoints marked <span class="font-medium text-neutral-800">Auth</span> require a valid Sanctum token.</li>
                <li>Email verification may apply to some flows after registration.</li>
            </ul>
        </section>

        @php
            $row = fn ($method, $path, $auth, $desc) => [$method, $path, $auth, $desc];
            $sections = [
                'auth' => [
                    'title' => 'Authentication',
                    'rows' => [
                        $row('POST', '/auth/register', 'No', 'Register a new customer account'),
                        $row('POST', '/auth/login', 'No', 'Login with email and password'),
                        $row('POST', '/auth/send-otp', 'No', 'Send OTP to phone'),
                        $row('POST', '/auth/verify-otp', 'No', 'Verify OTP and obtain token'),
                        $row('POST', '/auth/logout', 'Yes', 'Revoke current token'),
                        $row('GET', '/auth/me', 'Yes', 'Current user profile'),
                        $row('PUT', '/auth/profile', 'Yes', 'Update name, email, phone, and region (location)'),
                    ],
                ],
                'locations' => [
                    'title' => 'Locations (public)',
                    'rows' => [
                        $row('GET', '/locations/countries', 'No', 'List countries'),
                        $row('GET', '/locations/countries/{id}/governorates', 'No', 'Governorates in a country'),
                        $row('GET', '/locations/governorates/{id}/cities', 'No', 'Cities in a governorate'),
                        $row('GET', '/locations/cities/{id}/regions', 'No', 'Regions in a city'),
                    ],
                ],
                'animals' => [
                    'title' => 'Animals (public)',
                    'rows' => [
                        $row('GET', '/animals/categories', 'No', 'All animal categories'),
                        $row('GET', '/animals/categories/{id}', 'No', 'Animals in a category (with vaccines)'),
                        $row('GET', '/animals/{id}', 'No', 'Single animal with vaccines'),
                    ],
                ],
                'my-animals' => [
                    'title' => 'My animals',
                    'rows' => [
                        $row('GET', '/my-animals', 'Yes', 'List the authenticated user’s animals'),
                        $row('POST', '/my-animals', 'Yes', 'Register an animal (vaccine schedule generated)'),
                        $row('GET', '/my-animals/{id}', 'Yes', 'Animal details'),
                        $row('DELETE', '/my-animals/{id}', 'Yes', 'Remove animal from profile'),
                    ],
                ],
                'vaccines' => [
                    'title' => 'Vaccine schedules',
                    'rows' => [
                        $row('GET', '/vaccine-schedules', 'Yes', 'Schedules grouped by status'),
                        $row('PATCH', '/vaccine-schedules/{id}/mark-done', 'Yes', 'Mark a dose as administered'),
                    ],
                ],
                'reports' => [
                    'title' => 'Disease reports',
                    'rows' => [
                        $row('GET', '/reports/approved', 'No', 'Approved reports (e.g. for maps)'),
                        $row('GET', '/reports', 'Yes', 'Current user’s reports'),
                        $row('POST', '/reports', 'Yes', 'Submit a new report'),
                        $row('GET', '/reports/{id}', 'Yes', 'Single report'),
                    ],
                ],
                'notifications' => [
                    'title' => 'Notifications',
                    'rows' => [
                        $row('GET', '/notifications', 'Yes', 'Paginated notifications'),
                        $row('GET', '/notifications/unread-count', 'Yes', 'Unread count'),
                        $row('PATCH', '/notifications/{id}/read', 'Yes', 'Mark one as read'),
                        $row('POST', '/notifications/mark-all-read', 'Yes', 'Mark all as read'),
                    ],
                ],
            ];
        @endphp

        @foreach ($sections as $id => $section)
            <section id="{{ $id }}" class="mt-14 scroll-mt-20">
                <h2 class="text-xl font-bold text-neutral-900">{{ $section['title'] }}</h2>
                <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-neutral-200 bg-neutral-50 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                            <tr>
                                <th class="px-4 py-3">Method</th>
                                <th class="px-4 py-3">Path</th>
                                <th class="px-4 py-3">Auth</th>
                                <th class="px-4 py-3">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($section['rows'] as [$method, $path, $auth, $desc])
                                <tr class="align-top">
                                    <td class="whitespace-nowrap px-4 py-3 font-mono text-xs font-semibold text-emerald-800">{{ $method }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-neutral-800">{{ $base }}{{ $path }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-neutral-600">{{ $auth }}</td>
                                    <td class="px-4 py-3 text-neutral-600">{{ $desc }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach

        <section class="mt-14 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900">Email verification</h2>
            <p class="mt-2 text-sm text-neutral-600">
                Signed link (from verification email): <code class="rounded bg-neutral-100 px-1.5 py-0.5 font-mono text-xs">GET /api/email/verify/{id}/{hash}</code> — requires authentication and a valid signature.
            </p>
        </section>
    </main>

    <footer class="border-t border-neutral-200 py-8 text-center text-sm text-neutral-500">
        <a href="{{ url('/') }}" class="font-medium text-emerald-800 hover:text-emerald-900">ADAN</a>
        — Animal Disease Alert Network
    </footer>
</body>
</html>
