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

    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
</head>

<body class="antialiased bg-slate-100 dark:bg-slate-900">
    <!-- ========== HEADER ========== -->
    <header class="flex flex-wrap sm:justify-start sm:flex-nowrap lg:fixed lg:top-0 z-50 w-full bg-primary-500 shadow-[rgba(149,157,165,0.1)_0px_8px_24px] text-base py-2.5 sm:py-3">
        <nav class="max-w-[1550px] flex basis-full items-center w-full mx-auto px-4" aria-label="Global">
            <div class="me-5">
                <a class="flex-none text-xl font-semibold text-white dark:text-white dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" href="#" aria-label="Brand"><img src="/logo.png" class="h-12 md:h-9 w-auto" style="object-fit:contain;" /></a>
            </div>

            <div class="w-full flex items-center justify-end ms-auto sm:gap-x-3 text-black">
                <div class="flex flex-row items-center justify-end gap-2">
                    <div class="relative inline-flex" x-data="{open: false}" @click.outside="open = false">
                        <button type="button" class="w-[2.375rem] h-[2.375rem] inline-flex justify-center items-center gap-x-2 text-base font-semibold rounded-full bg-primary-400 text-gray-100 hover:opacity-60 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600" @click="open = !open">
                            <span>{{ str_split(Auth::user()->name)[0] }}</span>
                        </button>

                        <div id="dropdown" class="z-10 bg-white shadow-lg shadow-gray-500/10 divide-y divide-gray-100 border border-gray-200 dark:border-gray-400 w-44 dark:bg-gray-700 absolute lg:right-0 right-full lg:top-full block" :class="{'block': open, 'hidden': !open}">
                            <ul class="py-2 text-base text-gray-700 dark:text-gray-200">
                                <li>
                                    <form action="/logout" method="post">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Sign out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- ========== END HEADER ========== -->

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</body>

</html>
