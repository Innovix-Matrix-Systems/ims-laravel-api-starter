<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Observability Login</title>
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
        <main class="max-w-md mx-auto mt-6 sm:mt-10 px-4 sm:px-0">
            <div class="bg-white dark:bg-[#161615] p-6 sm:p-7 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
            <h1 class="text-lg font-medium mb-2">Sign in</h1>
            <p class="mb-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">Enter observability credentials to access the Observability dashboard and tools.</p>
            @if ($errors->any())
                <div class="mb-4 text-[#FF4433] text-sm">
                    {{ $errors->first() }}
                </div>
            @endif
            <form action="{{ url('observability-auth/login') }}" method="post" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded-sm bg-[#FDFDFC] dark:bg-[#161615] border-[#e3e3e0] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#e3e3e0] dark:focus:ring-[#3E3E3A]" required autocomplete="email">
                </div>
                <div>
                    <label class="block text-sm mb-1">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded-sm bg-[#FDFDFC] dark:bg-[#161615] border-[#e3e3e0] dark:border-[#3E3E3A] focus:outline-none focus:ring-2 focus:ring-[#e3e3e0] dark:focus:ring-[#3E3E3A]" required autocomplete="current-password">
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remember" class="size-5 accent-[#f53003] dark:accent-[#FF4433] border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <span>Remember</span>
                    </label>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1b1b18] text-white rounded-md hover:bg-black transition shadow-sm">Login</button>
                </div>
            </form>
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
