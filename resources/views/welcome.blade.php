@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="messages">
                    @include('layouts.messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <h2 class="display-4">Witaj w aplikacji Budżetomierz!</h2>

                        <p class="lead">
                            Aplikacja została stworzona, aby ułatwić Ci kontrolę nad finansami domowymi. Oferuje szereg
                            funkcji, które pomogą Ci śledzić wydatki, oszczędzać pieniądze oraz prognozować wydatki.
                        </p>

                        <h3 class="mt-4">Funkcje Aplikacji:</h3>

                        <ul class="list-group">
                            <li class="list-group-item"><strong>Dodawanie Transakcji:</strong> Śledź swoje wydatki, dodając
                                nowe transakcje w prosty sposób.</li>
                            <li class="list-group-item"><strong>Generowanie Raportów:</strong> Otrzymuj klarowne raporty
                                dotyczące swoich finansów, aby lepiej zrozumieć, gdzie idą Twoje pieniądze.</li>
                            <li class="list-group-item"><strong>Prognoza Wydatków:</strong> Planuj przyszłe wydatki,
                                korzystając z funkcji prognozowania.</li>
                            <li class="list-group-item"><strong>Cele Oszczędnościowe:</strong> Ustaw cele oszczędnościowe i
                                monitoruj ich postęp.</li>
                            <li class="list-group-item"><strong>Lista Zakupów:</strong> Twórz listy zakupów, aby lepiej
                                planować wydatki na codzienne potrzeby.</li>
                            <li class="list-group-item"><strong>Ranking Wydatków:</strong> Porównaj swoje wydatki z innymi
                                użytkownikami, aby zobaczyć, jak się plasujesz.</li>
                        </ul>

                        <h3 class="mt-4">Zacznij Zarządzać Swoimi Finansami Dziś!</h3>

                        <p>
                            <a class="btn btn-primary" href="{{ route('register') }}" role="button">Zarejestruj się</a>
                            lub
                            <a class="btn btn-outline-secondary" href="{{ route('login') }}" role="button">Zaloguj się</a>
                            aby rozpocząć korzystanie z naszej aplikacji i zacząć lepiej zarządzać swoim budżetem domowym.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
