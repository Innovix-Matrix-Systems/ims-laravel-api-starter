<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Observability Dashboard</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script>
            (function () {
                try {
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    var stored = localStorage.getItem('theme');
                    var dark = stored ? stored === 'dark' : prefersDark;
                    var c = document.documentElement.classList;
                    if (dark) c.add('dark'); else c.remove('dark');
                } catch (e) {}
            })();
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen">
        <x-observability.nav />
        <main class="max-w-5xl mx-auto px-4 py-8">
            <h1 class="text-xl font-medium mb-6">Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group bg-white dark:bg-[#161615] p-5 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-[#f5300320] dark:bg-[#FF443320] text-[#f53003] dark:text-[#FF4433]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 12.41A8.5 8.5 0 1 1 11.5 3.5c0 4.1 3.3 5.9 4.9 7.4 1.6 1.5 2.54 3.2 4.44 1.51z"/></svg>
                        </div>
                        <div class="font-medium">Health</div>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">Runs curated checks and shows pass/fail status. Useful for uptime probes and internal diagnostics.</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ url('health') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#1b1b18] text-white rounded-md hover:bg-black transition shadow-sm">
                            Open
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 7h10v10M7 17l10-10"/></svg>
                        </a>
                        <a href="https://spatie.be/docs/laravel-health/v1/introduction" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm hover:bg-[#f7f7f5] dark:hover:bg-[#1f1f1e] transition">Docs</a>
                    </div>
                </div>
                <div class="group bg-white dark:bg-[#161615] p-5 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-[#0ea5e920] dark:bg-[#38bdf820] text-[#0ea5e9] dark:text-[#38bdf8]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18"/><path d="M7 15l4-4 4 2 4-6"/></svg>
                        </div>
                        <div class="font-medium">Pulse</div>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">Captures performance metrics: slow requests, queries, jobs, caches, and outgoing calls for trend analysis.</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ url(config('pulse.path', 'pulse')) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#1b1b18] text-white rounded-md hover:bg-black transition shadow-sm">
                            Open
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 7h10v10M7 17l10-10"/></svg>
                        </a>
                        <a href="https://pulse.laravel.com/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm hover:bg-[#f7f7f5] dark:hover:bg-[#1f1f1e] transition">Docs</a>
                    </div>
                </div>
                <div class="group bg-white dark:bg-[#161615] p-5 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-[#22c55e20] dark:bg-[#86efac20] text-[#22c55e] dark:text-[#86efac]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="5"/><path d="M21 21l-4.3-4.3"/></svg>
                        </div>
                        <div class="font-medium">Telescope</div>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">Debugging console with request timeline, exceptions, logs, queries, jobs and more for development and QA.</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ url('telescope') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#1b1b18] text-white rounded-md hover:bg-black transition shadow-sm">
                            Open
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 7h10v10M7 17l10-10"/></svg>
                        </a>
                        <a href="https://laravel.com/docs/12.x/telescope" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm hover:bg-[#f7f7f5] dark:hover:bg-[#1f1f1e] transition">Docs</a>
                    </div>
                </div>
            </div>
        </main>
        <script>
            (function () {
                var btn = document.getElementById('theme-toggle');
                var sun = document.getElementById('icon-sun');
                var moon = document.getElementById('icon-moon');
                if (!btn) return;
                function syncIcons() {
                    var isDark = document.documentElement.classList.contains('dark');
                    if (isDark) { sun.classList.add('hidden'); moon.classList.remove('hidden'); }
                    else { moon.classList.add('hidden'); sun.classList.remove('hidden'); }
                }
                syncIcons();
                btn.addEventListener('click', function () {
                    var c = document.documentElement.classList;
                    var isDark = c.contains('dark');
                    if (isDark) {
                        c.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        c.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    syncIcons();
                });
            })();
        </script>
    </body>
</html>
