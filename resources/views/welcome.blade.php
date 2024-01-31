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

        section p {
            font-size: 16px;
            font-weight: 300;
        }

        button {
            max-width: 50%;
            border-radius: 50px !important;
        }

        #hero .col {
            justify-content: center;
            flex-direction: column;
            display: flex;
        }

        #hero .img-col {
            justify-content: flex-end;
            margin-top: 100px;
        }

        #hero img {
            max-width: 130% !important;
            width: 130%;
        }

        #hero .card {
            box-shadow: 11px 7px 16px #f9f9f9;
            border: 0;
            text-align: center;
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
                    <h1>Software<br>Development</h1>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec odio. Quisque volutpat mattis eros.
                        Nullam malesuada erat ut turpis. Suspendisse urna nibh, viverra non, semper suscipit, posuere a,
                        pede.</p>
                    <button type="button" class="btn btn-dark btn-large">Learn more</button>
                </div>
                <div class="col img-col">
                    <img src="./assets/hero-img.png" class="img-fluid" alt="Software Development">
                </div>
            </div>
            <div class="row cards">

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <img src="./assets/icon1.svg" class="icon" alt="Service One" />
                            <h5 class="card-title">Web Dev</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                                the card's content.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <img src="./assets/icon2.svg" class="icon" alt="Service Two" />
                            <h5 class="card-title">Machine Learning</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                                the card's content.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <img src="./assets/icon3.svg" class="icon" alt="Service Three" />
                            <h5 class="card-title">Security</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                                the card's content.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
