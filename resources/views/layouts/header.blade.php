<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Budżetomierz</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style type="text/css">
        i {
            font-size: 50px;
        }
    </style>
</head>

<body>
    <div id="app">
        @section('navBar')
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Budżetomierz
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Zaloguj') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Zarejestruj') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('ranking.index') }}" class="btn btn-sm nav-link">
                                        <i class="bi bi-bar-chart-line-fill align-middle" style="font-size: 1rem;"></i>
                                        Ranking
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('transactions.index') }}" class="btn btn-sm nav-link">
                                        <i class="bi bi-cash-coin align-middle" style="font-size: 1rem;"></i>
                                        Transakcje
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('savings-plans.index') }}" class="btn btn-sm nav-link">
                                        <i class="bi bi-piggy-bank align-middle" style="font-size: 1rem;"></i>
                                        Plany oszczędnościowe
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('shopping-lists.index') }}" class="btn btn-sm nav-link">
                                        <i class="bi bi-list-task align-middle" style="font-size: 1rem;"></i>
                                        Listy zakupów
                                    </a>
                                </li>
                                @if (Auth::user()->id_role === 1)
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown" class="nav-link dropdown-toggle bg-primary text-white bg-opacity-75" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            <i class="bi bi-person-fill-gear align-middle" style="font-size: 1rem;"></i>
                                            Panel ADMINISTRATORA
                                        </a>
                                @endif
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.userList') }}">Lista użytkowników</a>
                                </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <i class="bi bi-person-circle align-middle" style="font-size: 1rem;"></i>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('profile.index') }}">Mój profil</a>
                                        <a class="dropdown-item" href="{{ route('category.list') }}">Moje kategorie</a>

                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Wyloguj') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        @show
