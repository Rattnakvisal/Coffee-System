<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <script>
        (() => {
            try {
                if (sessionStorage.getItem('coffee:disable-admin-menu-animation') === '1') {
                    document.documentElement.classList.add('admin-menu-navigation-instant');
                    sessionStorage.removeItem('coffee:disable-admin-menu-animation');
                }
            } catch (error) {
                // Ignore blocked storage and keep the normal page behavior.
            }
        })();
    </script>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/index.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#fbf9f6] text-[#2c221e] font-sans antialiased selection:bg-[#f4a06b]/30 selection:text-[#78350f]">
    @yield('content')
</body>

</html>
