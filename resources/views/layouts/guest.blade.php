<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AbsenKu') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-absenku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">

    @yield('content')

    <button id="scroll-to-top"
            type="button"
            class="fixed bottom-5 right-5 z-[60] hidden h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <span class="sr-only">Scroll ke atas</span>
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const inputId = button.dataset.passwordToggle;
                const input = document.getElementById(inputId);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                const showIcon = button.querySelector('[data-icon="show"]');
                const hideIcon = button.querySelector('[data-icon="hide"]');
                if (showIcon && hideIcon) {
                    showIcon.classList.toggle('hidden', !isPassword);
                    hideIcon.classList.toggle('hidden', isPassword);
                }
            });
        });

        const scrollButton = document.getElementById('scroll-to-top');
        if (scrollButton) {
            const toggleScrollButton = () => {
                if (window.scrollY > 320) {
                    scrollButton.classList.remove('hidden');
                    scrollButton.classList.add('flex');
                } else {
                    scrollButton.classList.add('hidden');
                    scrollButton.classList.remove('flex');
                }
            };
            toggleScrollButton();
            window.addEventListener('scroll', toggleScrollButton);
            scrollButton.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    </script>

</body>
</html>
