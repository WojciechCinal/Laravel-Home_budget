@extends('layouts.app')

@section('content')
    <style>
        section {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        section h1 {
            text-transform: uppercase;
            font-weight: 900;
            color: #0d6efd;
            text-align: left;
            margin-bottom: 20px;
        }

        h3{
            font-weight: 600;
            color: #0d6efd;
        }

        section p {
            font-size: 16px;
            font-weight: 300;
        }

        section i {
            font-size: 16px;
            font-weight: 300;
        }


        #hero .col {
            justify-content: center;
            flex-direction: column;
            display: flex;
        }

        #hero .img-col {
            margin-right: 30px;
            margin-top: 10px;
        }

        #hero img {
            max-width: 100% !important;
            width: 100%;
        }

        #hero .card {
            box-shadow: 11px 7px 16px #f9f9f9;
            border: 1;
            border-color: #0d6efd;
            text-align: center;
            margin-top: 2rem;
        }

        #hero .icon {
            width: 50px;
            height: 50px;
            margin-bottom: 20px;
        }
    </style>
    <section id="hero">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>Budżetomierz</h1>
                    <h4>Witaj w aplikacji Budżetomierz!</h4>
                    <p>Aplikacja została stworzona, aby ułatwić Ci kontrolę nad finansami domowymi. Oferuje szereg
                        funkcji, które pomogą Ci śledzić wydatki, oszczędzać pieniądze oraz prognozować wydatki.
                        Możesz również generować raporty z historii transakcji i planować listy zakupów.</p>
                    <a href="{{ route('about') }}" class="btn btn-dark btn-large"
                        style="max-width: 50%; border-radius: 50px !important;">Dowiedz się wiecej</a>
                </div>
                <div class="col img-col">
                    <img src="/img/page_main_icon.png" class="img-fluid" alt="budżetomierz start img">
                </div>
            </div>
            <div class="row cards">

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/money.png" class="icon" alt="Historia transkacji PNG" />
                            <h5 class="card-title">Historia transkacji</h5>
                            <p class="card-text">Śledź swoje wydatki, dodając nowe transakcje w prosty sposób. Dla łatwego
                                podziału transakcji masz do dyspozycji różne kategorie i podkategorie, które możesz
                                dostosować do własnych potrzeb.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/stock-market.png" class="icon" alt="Generowanie raportów PNG" />
                            <h5 class="card-title">Generowanie raportów</h5>
                            <p class="card-text"> Twórz klarowne raporty dotyczące swoich finansów, aby lepiej zrozumieć,
                                na co wydajesz Twoje pieniądze. Każdy raport, w uproszczonej formie, możesz pobrać w
                                formacie <i>.pdf</i> </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/marketing.png" class="icon" alt="Prognoza wydatków PNG" />
                            <h5 class="card-title">Prognoza wydatków</h5>
                            <p class="card-text">Planuj przyszłe wydatki, korzystając z funkcji prognozowania. Funkcja ta
                                pozwala porównać obecne wydatki miesięcze z poprzednimi latami.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row cards">

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/savings.png" class="icon" alt="Cele oszczędnościowe PNG" />
                            <h5 class="card-title">Cele oszczędnościowe</h5>
                            <p class="card-text">Spełniaj swoje marzenia! Ustaw cele oszczędnościowe i monitoruj ich postęp.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/list.png" class="icon" alt="Listy zakupów PNG" />
                            <h5 class="card-title">Listy zakupów</h5>
                            <p class="card-text">Twórz listy zakupów, aby lepiej planować wydatki na codzienne potrzeby.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            <img src="/img/pagerank.png" class="icon" alt="Ranking wydatków PNG" />
                            <h5 class="card-title">Ranking wydatków</h5>
                            <p class="card-text">Porównaj swoje wydatki ze średnią wydatków na kategorię innych
                                użytkowników, aby zobaczyć, jak wypadają twoje wydatki.</p>
                        </div>
                    </div>
                </div>
            </div>
            @guest
                <div class="text-center mt-3">
                    <h3 class="mt-4">Zacznij Zarządzać Swoimi Finansami Dziś!</h3>

                    <p>
                        <a class="btn btn-primary" href="{{ route('register') }}" role="button">Zarejestruj się</a>
                        lub
                        <a class="btn btn-outline-secondary" href="{{ route('login') }}" role="button">Zaloguj się</a>
                        aby rozpocząć korzystanie z aplikacji i zacząć lepiej zarządzać swoim budżetem domowym.
                    </p>
                </div>
            @endguest
        </div>
    </section>
@endsection
