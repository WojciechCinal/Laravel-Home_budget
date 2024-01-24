@extends('layouts.app')

@section('content')
    @include('Ranking.modal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="mt-2 ms-3">{{ $rankingName }}</h3>
                        <a href="#" class="btn btn-primary btn-sm me-3" data-bs-toggle="modal"
                            data-bs-target="#generateRankingModal">
                            <i class="bi bi-calendar-week-fill align-middle" style="font-size: 1.5rem;"></i> Zmień przedział
                            rankingu
                        </a>
                    </div>
                    <div class="card-body">
                        <div style="width: 100%;">
                            <canvas id="myChart"></canvas>
                        </div>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered border-secondary">
                                <thead>
                                    <tr class="table-dark">
                                        <th>Kategoria</th>
                                        @foreach ($myExpenses as $category => $expense)
                                            <td class="text-center">{{ $category }}</td>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <th>Moje wydatki (PLN)</th>
                                        @foreach ($myExpenses as $category => $expense)
                                            <td class="text-center">{{ $expense }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctxBar = document.getElementById('myChart').getContext('2d');

            var labels = {!! json_encode($averageAmounts->keys()->all()) !!};
            var averageAmountsData = {!! json_encode($averageAmounts->values()->all()) !!};
            var myExpensesData = {!! json_encode($myExpenses->values()->all()) !!};

            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Średnie wydatki',
                            data: averageAmountsData,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderWidth: 1
                        },
                        {
                            label: 'Moje wydatki',
                            data: myExpensesData,
                            backgroundColor: 'rgba(255, 206, 86, 0.8)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
