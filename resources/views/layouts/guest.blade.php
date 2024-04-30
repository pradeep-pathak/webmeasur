<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-gray-900 antialiased bg-[#eaeffb] dark:bg-slate-700">
    <!-- ========== HEADER ========== -->
    <header class="flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-primary-600 text-white shadow-[rgba(149,157,165,0.1)_0px_8px_24px] text-sm py-4 absolute top-0">
        <nav class="max-w-[85rem] w-full mx-auto px-4 sm:flex sm:items-center" aria-label="Global">
            <a class="flex-none" href="#">
                <img src="/logo.png" alt="" class="w-64">
            </a>
        </nav>
    </header>
    <!-- ========== END HEADER ========== -->

    <div class="lg:min-h-screen flex flex-col sm:justify-center items-center pt-24 sm:pt-0">
        <div class="w-full sm:max-w-lg p-6 bg-white dark:bg-black shadow-[rgba(149,157,165,0.1)_0px_8px_24px] rounded overflow-hidden">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
