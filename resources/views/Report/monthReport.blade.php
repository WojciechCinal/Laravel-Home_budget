@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="messages">
            @include('layouts.messages')
        </div>
        <form id="monthlyReportForm" action="{{ route('generate.monthly.report.pdf') }}" method="GET">
            <input type="hidden" name="selected_year" value="{{ request()->input('selected_year', now()->year) }}">
            <input type="hidden" name="start_date" value="{{ request()->input('start_date', 1) }}">
            <input type="hidden" name="end_date" value="{{ request()->input('end_date', 12) }}">

            @foreach (request()->input('categories', []) as $category)
                <input type="hidden" name="categories[]" value="{{ $category }}">
            @endforeach
            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg" id="downloadReportButton"><i
                        class="bi bi-file-earmark-pdf-fill mt-2" style="font-size: 1.5rem;"></i> POBIERZ RAPORT</button>
            </div>
        </form>

        @php
            $year = request('selected_year');
        @endphp

        @foreach ($transactionsByMonth as $month => $transactionsInMonth)
            <div class="accordion mt-3" id="accordion_{{ $month }}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTable_{{ $month }}">
                        <button class="accordion-button bg-info" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTable_{{ $month }}" aria-expanded="true"
                            aria-controls="collapseTable_{{ $month }}">
                            <h4><b>{{ \Carbon\Carbon::createFromFormat('m', $month)->locale('pl')->isoFormat('MMMM') }}
                                    {{ $year }} - Raport miesięczny</b></h4>
                        </button>
                    </h2>
                    <div id="collapseTable_{{ $month }}" class="accordion-collapse collapse show"
                        aria-labelledby="headingTable_{{ $month }}">
                        <div class="accordion-body">
                            <table class="table table-responsive table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kategoria</th>
                                        @foreach ($transactionsByWeek[$month] as $week)
                                            <th class="text-center">{{ $week['week_dates'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->name_category }}</td>
                                            @foreach ($transactionsByWeek[$month] as $week => $transactionsInWeek)
                                                @if ($transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') == 0)
                                                    <td class="text-center">
                                                        -
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        {{ $transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                                            0 }}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr class="table-success">
                                        <td>Łącznie (PLN):</td>
                                        @foreach ($weekTotals[$month] as $weekTotal)
                                            <td class="text-center">{{ $weekTotal }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCharts_{{ $month }}">
                        <button class="accordion-button collapsed text-bg-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCharts_{{ $month }}"
                            aria-expanded="false" aria-controls="collapseCharts_{{ $month }}">
                            <h5><b>Wykresy - zobrazowanie miesięcznych wydatków.</b></h5>
                        </button>
                    </h2>
                    <div id="collapseCharts_{{ $month }}" class="accordion-collapse collapse"
                        aria-labelledby="headingCharts_{{ $month }}">
                        <div class="accordion-body">
                            <!-- Wykres słupkowy -->
                            @php
                                $barChartId = 'myBarChart_' . $month;
                            @endphp
                            <canvas id="{{ $barChartId }}"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var ctxBar = document.getElementById('{{ $barChartId }}').getContext('2d');
                                    var myBarChart = new Chart(ctxBar, {
                                        type: 'bar',
                                        data: {
                                            labels: {!! json_encode(array_keys($monthTotalsCat[$month])) !!},
                                            datasets: [{
                                                label: 'Miesięczne wydatki',
                                                data: {!! json_encode(array_values($monthTotalsCat[$month])) !!},
                                                backgroundColor: [
                                                    'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                                                    'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                                                    'rgba(255, 99, 132, 0.8)',
                                                    'rgba(75, 192, 192, 0.8)', 'rgba(54, 162, 235, 0.8)',
                                                    'rgba(255, 206, 86, 0.8)',
                                                    'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)',
                                                    'rgba(255, 159, 64, 0.8)'
                                                ],
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

                            <!-- Wykresy kołowe dla podkategorii -->
                            <div class="row mt-3">
                                @foreach ($monthTotalsSubCat[$month] as $categoryName => $subCategories)
                                    <div class="col-md-4 mt-2">
                                        <div class="card my-2">
                                            <div class="card-header text-bg-secondary">
                                                <b>{{ $categoryName }} </b>
                                            </div>
                                            <div class="card-body" style="max-height: 300px">
                                                @php
                                                    $pieChartId = 'myPieChart_' . $month . '_' . str_replace(' ', '_', $categoryName);
                                                @endphp
                                                <canvas id="{{ $pieChartId }}"></canvas>
                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function() {
                                                        var ctx = document.getElementById('{{ $pieChartId }}').getContext('2d');
                                                        var myPieChart = new Chart(ctx, {
                                                            type: 'doughnut',
                                                            data: {
                                                                labels: {!! json_encode(array_keys($subCategories)) !!},
                                                                datasets: [{
                                                                    data: {!! json_encode(array_values($subCategories)) !!},
                                                                    backgroundColor: [
                                                                        'rgba(54, 162, 235, 0.8)',
                                                                        'rgba(75, 192, 92, 0.8)',
                                                                        'rgba(255, 206, 86, 0.8)',
                                                                        'rgba(153, 102, 255, 0.8)',
                                                                        'rgba(255, 159, 64, 0.8)',
                                                                        'rgba(255, 99, 132, 0.8)',
                                                                        'rgba(75, 192, 192, 0.8)',
                                                                        'rgba(54, 162, 235, 0.8)',
                                                                        'rgba(255, 206, 86, 0.8)',
                                                                        'rgba(153, 102, 255, 0.8)',
                                                                        'rgba(255, 159, 64, 0.8)'
                                                                    ],
                                                                    borderColor: 'rgba(75, 192, 192, 1)',
                                                                    borderWidth: 1
                                                                }]
                                                            },
                                                            options: {
                                                                plugins: {
                                                                    legend: {
                                                                        position: 'bottom'
                                                                    }
                                                                },
                                                                animation: {
                                                                    animateScale: true,
                                                                    animateRotate: true
                                                                }
                                                            }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        $(document).ready(function() {
            $('#downloadReportButton').click(function() {
                $.ajax({
                    success: function(response) {
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(error) {
                        console.log(error);
                        alert('Wystąpił błąd podczas pobierania raportu.');
                    }
                });
            });
        });
    </script>
@endsection
