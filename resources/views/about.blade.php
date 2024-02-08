@extends('layouts.app')
<style>
    h2,
    .h2,
    h1,
    .h1 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-weight: 700;
        line-height: 1.2;
    }

    h1,
    .h1 {
        font-size: calc(1.375rem + 1.5vw);
    }

    @media (min-width: 1200px) {

        h1,
        .h1 {
            font-size: 2.5rem;
        }
    }

    h2,
    .h2 {
        font-size: calc(1.325rem + 0.9vw);
    }

    @media (min-width: 1200px) {

        h2,
        .h2 {
            font-size: 2rem;
        }
    }


    .showcase .showcase-text {
        padding: 3rem;
    }

    .showcase .showcase-img {
        min-height: 30rem;
        background-size: cover;
    }

    @media (min-width: 768px) {
        .showcase .showcase-text {
            padding: 7rem;
        }
    }

    .lead {
        font-size: 1.25rem;
        font-weight: 300;
    }

    .container,
    .container-fluid,
    .container-xxl,
    .container-xl,
    .container-lg,
    .container-md,
    .container-sm {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 0;
        width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-right: auto;
        margin-left: auto;
    }
</style>

@section('content')
    <h1 class="text-center mb-3"> O aplikacji słów kilka...</h1>
    <section class="showcase">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('img/clement-helardot-95YRwf6CNw8-unsplash.jpg')"></div>
                <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                    <h2>Czym jest Budżetomierz?</h2>
                    <p class="lead mb-0">Jest to aplikacja webowa do zarządzania budżetem domowym, jej głównym celem jest
                        umożliwienie użytkownikowi efektywne i intuicyjne zarządzanie budżetem domowym.</p>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-lg-6 text-white showcase-img" style="background-image: url('img/towfiqu-barbhuiya-3aGZ7a97qwA-unsplash.jpg')"></div>
                <div class="col-lg-6 my-auto showcase-text">
                    <h2>Dlaczego powstał?</h2>
                    <p class="lead mb-0">Pieniądze od wieków odgrywają istotną rolę w życiu człowieka, pełniąc funkcję
                        środka wymiany. Zarówno w starożytnych społecznościach, jak i dzisiaj w globalnym społeczeństwie
                        wpływają one na niemal wszystkie aspekty naszego codziennego życia, stając się jego nieodłącznym
                        elementem. Dlatego zarządzanie pieniędzmi ma ogromne znaczenie.</p>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('img/mathieu-stern-1zO4O3Z0UJA-unsplash.jpg')"></div>
                <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                    <h2>Mocne strony aplikacji.</h2>
                    <p class="lead mb-0">
                        <li class="lead">Intuicyjny i przejrzysty interferjs.</li>
                        <li class="lead">Łatwe prowadzenie historii wydatków.</li>
                        <li class="lead">Rozbudowane ale klarowne raporty dotyczące transakcji.</li>
                        <li class="lead">Prognozowanie przyszłych wydatków.</li>
                        <li class="lead">Aplikacja jest darmowa.</li>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
