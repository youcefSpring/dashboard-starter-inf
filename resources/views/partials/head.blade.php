<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ filled($title ?? null) ? $title.' · ' : '' }}{{ config('app.name') }}</title>

<script>
    // Apply the saved theme, layout and sidebar state before first paint to avoid a flash.
    try {
        const theme = localStorage.getItem('theme');
        if (theme === 'light' || theme === 'dark') document.documentElement.dataset.theme = theme;
        if (localStorage.getItem('layout') === 'vertical') document.documentElement.dataset.layout = 'vertical';
        if (localStorage.getItem('sidebar-collapsed') === '1') document.documentElement.classList.add('sb-collapsed');
    } catch (e) { /* storage unavailable */ }
</script>

@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
