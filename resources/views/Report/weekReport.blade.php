@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="messages">
            @include('layouts.messages')
        </div>
        <form id="weeklyReportForm" action="{{ route('generate.weekly.report.pdf') }}" method="GET">
            <input type="hidden" name="startWeek" value="{{ request()->input('startWeek', now()->format('Y-\WW')) }}">
            <input type="hidden" name="endWeek" value="{{ request()->input('endWeek', now()->addWeeks(1)->format('Y-\WW')) }}">

            @foreach (request()->input('categories', []) as $category)
                <input type="hidden" name="categories[]" value="{{ $category }}">
            @endforeach

            <button type="submit" class="btn btn-primary">Pobierz raport</button>
        </form>

        @foreach ($transactionsByWeek as $week => $weekTransactions)
            <div class="accordion mt-3" id="accordion_{{ $week }}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTable_{{ $week }}">
                        <button class="accordion-button bg-info" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTable_{{ $week }}" aria-expanded="true"
                            aria-controls="collapseTable_{{ $week }}">
                            @php
                                $carbonWeek = \Carbon\Carbon::parse($week);
                                $year = $carbonWeek->format('Y');
                                $weekNumber = $carbonWeek->format('W');
                                $startDay = $carbonWeek->startOfWeek()->translatedFormat('d M');
                                $endDay = $carbonWeek->endOfWeek()->translatedFormat('d M');
                            @endphp

                            <h4><b>{{ $year }}r. tydz. {{ $weekNumber }}: {{ $startDay }} -
                                    {{ $endDay }}</b></h4>

                        </button>
                    </h2>
                    <div id="collapseTable_{{ $week }}" class="accordion-collapse collapse show"
                        aria-labelledby="headingTable_{{ $week }}">
                        <div class="accordion-body">
                            <table class="table table-responsive table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kategoria</th>
                                        @foreach ($transactionsByDay[$week]->keys() as $day)
                                            <th class="text-center">
                                                {{ \Carbon\Carbon::parse($day)->translatedFormat('d M') }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->name_category }}</td>
                                            @foreach ($transactionsByDay[$week]->keys() as $day)
                                                <td class="text-center">
                                                    @php
                                                        $dayTransactions = $transactionsByDay[$week][$day];
                                                        $categoryTotal = $dayTransactions->where('category.name_category', $category->name_category)->sum('amount_transaction');
                                                    @endphp

                                                    @if ($categoryTotal == 0)
                                                        -
                                                    @else
                                                        {{ $categoryTotal }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr class="table-success">
                                        <td>Łącznie (PLN):</td>
                                        @foreach ($dayTotals[$week] as $dayTotal)
                                            <td class="text-center">{{ $dayTotal }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCharts_{{ $week }}">
                        <button class="accordion-button collapsed text-bg-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCharts_{{ $week }}"
                            aria-expanded="false" aria-controls="collapseCharts_{{ $week }}">
                            <h5><b>Wykresy - zobrazowanie tygodniowych wydatków.</b></h5>
                        </button>
                    </h2>
                    <div id="collapseCharts_{{ $week }}" class="accordion-collapse collapse"
                        aria-labelledby="headingCharts_{{ $week }}">
                        <div class="accordion-body">
                            <!-- Wykres słupkowy -->
                            @php
                                $barChartId = 'myBarChart_' . $week;
                            @endphp
                            <canvas id="{{ $barChartId }}"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var ctxBar = document.getElementById('{{ $barChartId }}').getContext('2d');
                                    var myBarChart = new Chart(ctxBar, {
                                        type: 'bar',
                                        data: {
                                            labels: {!! json_encode(array_keys($weekTotalsCat[$week])) !!},
                                            datasets: [{
                                                label: 'Tygodniowe wydatki',
                                                data: {!! json_encode(array_values($weekTotalsCat[$week])) !!},
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
                                @foreach ($weekTotalsSubCat[$week] as $categoryName => $subCategories)
                                    <div class="col-md-4 mt-2">
                                        <div class="card my-2">
                                            <div class="card-header text-bg-secondary">
                                                <b>{{ $categoryName }} </b>
                                            </div>
                                            <div class="card-body" style="max-height: 300px">
                                                @php
                                                    $pieChartId = 'myPieChart_' . $week . '_' . str_replace(' ', '_', $categoryName);
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
@endsection
