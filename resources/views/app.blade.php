<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Ingco')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    {{-- tailwind link --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite('resources/css/app.css')
</head>

<body class="antialiased bg-slate-100">
    <div
        class="relative sm:flex min-h-screen bg-dots-darker bg-center bg-white-100 dark:bg-dots-lighter dark:bg-white-900 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ route('tasks.index') }}"
                        class="font-semibold text-black-600 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Tareas</a>
                    <a href="{{ route('logout') }}"
                        class="ml-4 font-semibold text-black-600 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar
                        Sesión</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="font-semibold text-black-600 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="ml-4 font-semibold text-black-600 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Registro</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="mx-auto p-6 lg:p-8">
            @yield('content')
        </div>
    </div>
</body>

</html>
