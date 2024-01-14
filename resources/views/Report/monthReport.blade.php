@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach ($transactionsByMonth as $month => $transactionsInMonth)
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ $month }}</div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kategoria</th>
                                        @foreach ($transactionsByWeek[$month] as $week)
                                            <th>{{ $week['week_dates'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->name_category }}</td>
                                            @foreach ($transactionsByWeek[$month] as $week => $transactionsInWeek)
                                                <td>
                                                    {{ $transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                                        0 }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>Łącznie</td>
                                        @foreach ($weekTotals[$month] as $weekTotal)
                                            <td>{{ $weekTotal }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            <canvas id="myChart{{ $loop->index }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <!-- Skrypt dla Chart.js -->
    @foreach ($transactionsByMonth as $month => $transactionsInMonth)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var ctx = document.getElementById('myChart{{ $loop->index }}').getContext('2d');

                // Pobierz nazwy kategorii z monthTotals
                var categoryLabels = {!! json_encode(array_keys($monthTotals[$month])) !!};

                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Miesięczne wydatki',
                            data: {!! json_encode(array_values($monthTotals[$month])) !!},
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                                'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(75, 192, 192, 0.8)', 'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)'
                            ],
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
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
    @endforeach
@endsection
