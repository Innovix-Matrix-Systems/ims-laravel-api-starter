<nav class="bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('observability') }}" class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Observability</a>
        <div class="flex items-center gap-3">
            <button id="theme-toggle" type="button" aria-label="Toggle theme" class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f7f7f5] dark:hover:bg-[#1f1f1e] transition">
                <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
            @if (session('observability_authed') === true)
                <span class="text-sm">{{ session('observability_email') }}</span>
                <a href="{{ url('observability-auth/logout') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-[#1b1b18] text-white hover:bg-black transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                    Logout
                </a>
            @else
                <a href="{{ url('observability-auth/login') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f7f7f5] dark:hover:bg-[#1f1f1e] transition">Login</a>
            @endif
        </div>
    </div>
</nav>
