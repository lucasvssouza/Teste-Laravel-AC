<!DOCTYPE html>
<html lang="pt-BR" class="h-100">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100 theme-bg theme-text" id="app-body">
    @include('partials.navbar')

    <main class="flex-fill d-flex justify-content-center align-items-center px-3 theme-bg theme-text">
        @yield('content')
    </main>

    @include('partials.footer')
    @yield('scripts')

</body>
</html>
