<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ADAN — Animal Disease Alert Network. Early warning, vaccine tracking, and geo-based disease surveillance for animal health.">

    <title>ADAN — Animal Disease Alert Network</title>

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
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-white drop-shadow-sm">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                    <svg class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v6l3 2" />
                    </svg>
                </span>
                ADAN
            </a>
            <div class="flex items-center gap-3 text-sm font-medium">
                <a href="#about" class="hidden rounded-md px-3 py-2 text-white/90 hover:bg-white/10 hover:text-white sm:inline">About</a>
                <a href="#how-it-works" class="hidden rounded-md px-3 py-2 text-white/90 hover:bg-white/10 hover:text-white sm:inline">How it works</a>
                @if (Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="rounded-lg bg-white/95 px-4 py-2 text-emerald-900 shadow-sm ring-1 ring-black/5 transition hover:bg-white"
                    >
                        Staff login
                    </a>
                @endif
            </div>
        </nav>
    </header>

    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-950 via-teal-950 to-emerald-950 pb-24 pt-28 text-white lg:pb-32 lg:pt-36">
        <div class="pointer-events-none absolute -left-32 top-0 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-teal-400/15 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.04%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-60" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
            <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium uppercase tracking-wider text-emerald-200 ring-1 ring-white/15">
                Animal Disease Alert Network
            </p>
            <h1 class="max-w-3xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                Protect herds and flocks with early warning and trusted veterinary care.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-emerald-100/90">
                ADAN connects breeders with veterinarians to track vaccines, report suspected disease, and deliver
                location-aware alerts when outbreaks are confirmed—so communities can respond before losses spread.
            </p>
            <div class="mt-10 flex flex-wrap items-center gap-4">
                <a
                    href="#value"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-400 px-6 py-3 text-base font-semibold text-emerald-950 shadow-lg shadow-emerald-950/30 transition hover:bg-emerald-300"
                >
                    See what ADAN does
                </a>
                @if (Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/25 bg-white/5 px-6 py-3 text-base font-semibold text-white backdrop-blur transition hover:bg-white/10"
                    >
                        Open admin console
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section id="about" class="relative -mt-12 px-6 lg:px-8">
        <div class="mx-auto grid max-w-6xl gap-6 rounded-2xl bg-white p-8 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-200/80 sm:grid-cols-3 sm:p-10">
            <div class="text-center sm:text-left">
                <p class="text-3xl font-bold text-emerald-700">Geo-aware</p>
                <p class="mt-1 text-sm text-neutral-600">Alerts tied to countries, governorates, cities, and regions.</p>
            </div>
            <div class="text-center sm:border-x sm:border-neutral-200 sm:px-6 sm:text-left">
                <p class="text-3xl font-bold text-emerald-700">Proactive</p>
                <p class="mt-1 text-sm text-neutral-600">Vaccine schedules and reminders before animals are due.</p>
            </div>
            <div class="text-center sm:text-left">
                <p class="text-3xl font-bold text-emerald-700">Collaborative</p>
                <p class="mt-1 text-sm text-neutral-600">Breeders report; doctors verify and notify the community.</p>
            </div>
        </div>
    </section>

    <section id="value" class="mx-auto max-w-6xl px-6 py-20 lg:px-8 lg:py-28">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-neutral-900 sm:text-4xl">Built for real-world animal health</h2>
            <p class="mt-4 text-lg text-neutral-600">
                The platform turns scattered observations into a structured network: standardized data, clear roles, and timely communication when it matters most.
            </p>
        </div>

        <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-neutral-900">Early warning</h3>
                <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                    Suspected cases can be reported and reviewed so confirmed disease triggers alerts—not rumors.
                </p>
            </article>

            <article class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-neutral-900">Vaccine discipline</h3>
                <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                    Register animals by category and keep schedules visible—with reminders when doses are due.
                </p>
            </article>

            <article class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-neutral-900">Regional awareness</h3>
                <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                    Approved reports feed maps and notifications so risk is understood in the right place and time.
                </p>
            </article>

            <article class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-neutral-900">One network</h3>
                <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                    Customers manage their animals; doctors oversee data, approvals, and outreach from one admin hub.
                </p>
            </article>
        </div>
    </section>

    <section id="how-it-works" class="border-y border-neutral-200 bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight text-neutral-900 sm:text-4xl">How the concept comes together</h2>
            <p class="mt-4 max-w-2xl text-lg text-neutral-600">
                ADAN is both a record system and a communication layer: it organizes geography and species data, then uses that structure to target alerts and care.
            </p>

            <ol class="mt-14 grid gap-10 md:grid-cols-3">
                <li class="relative pl-10">
                    <span class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">1</span>
                    <h3 class="font-semibold text-neutral-900">Register &amp; plan</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                        Animals are linked to categories and vaccines; schedules are generated so nothing falls through the cracks.
                    </p>
                </li>
                <li class="relative pl-10">
                    <span class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">2</span>
                    <h3 class="font-semibold text-neutral-900">Observe &amp; report</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                        When something looks wrong, breeders submit reports with context; the network captures where and when it happened.
                    </p>
                </li>
                <li class="relative pl-10">
                    <span class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">3</span>
                    <h3 class="font-semibold text-neutral-900">Verify &amp; alert</h3>
                    <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                        Veterinarians validate findings; approved cases drive notifications and maps so others nearby can act early.
                    </p>
                </li>
            </ol>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-20 lg:px-8 lg:py-24">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-800 to-teal-900 px-8 py-12 text-center text-white shadow-xl sm:px-14 sm:py-16">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Ready to work with the data layer?</h2>
            <p class="mx-auto mt-4 max-w-xl text-emerald-100/95">
                The REST API powers mobile and web clients; the Filament admin is the operations home for doctors and staff.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a
                    href="{{ route('docs.api') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-emerald-900 shadow-md transition hover:bg-emerald-50"
                >
                    API documentation
                </a>
                @if (Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15"
                    >
                        Sign in to admin
                    </a>
                @endif
            </div>
        </div>
    </section>

    <footer class="border-t border-neutral-200 bg-neutral-50 py-10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 text-center text-sm text-neutral-500 sm:flex-row sm:text-left lg:px-8">
            <p>
                <span class="font-semibold text-neutral-700">ADAN</span>
                — Animal Disease Alert Network. {{ date('Y') }}.
            </p>
            <p class="max-w-md">
                {{ config('app.name') !== 'Laravel' ? config('app.name') . ' · ' : '' }}Laravel, Filament, and Sanctum.
            </p>
        </div>
    </footer>
</body>
</html>
