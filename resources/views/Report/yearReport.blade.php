@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="messages">
            @include('layouts.messages')
        </div>
        <form id="yearlyReportForm" action="{{ route('generate.yearly.report.pdf') }}" method="GET" style="display: none;">
            <input type="hidden" name="start_year" id="start_year" value="{{ request()->input('start_year', now()->year) }}">
            <input type="hidden" name="end_year" id="end_year" value="{{ request()->input('end_year', now()->year) }}">
            @foreach (request()->input('categories', []) as $category)
                <input type="hidden" name="categories[]" value="{{ $category }}">
            @endforeach
        </form>

        <button type="button" onclick="generateYearlyReport()" class="btn btn-primary">Pobierz raport</button>
        @foreach ($yearlyExpenses as $year => $yearData)
            <div class="accordion mt-3" id="accordion_{{ $year }}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTable_{{ $year }}">
                        <button class="accordion-button bg-info" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTable_{{ $year }}" aria-expanded="true"
                            aria-controls="collapseTable_{{ $year }}">
                            <h4><b> {{ $year }} - Roczne zestawienie wydatków. </b></h4>
                        </button>
                    </h2>
                    <div id="collapseTable_{{ $year }}" class="accordion-collapse collapse show"
                        aria-labelledby="headingTable_{{ $year }}">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nazwa kategorii</th>
                                            @for ($month = 1; $month <= 12; $month++)
                                                @php
                                                    $monthName = \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
                                                    $shortMonthName = Str::limit($monthName, 3, '');
                                                @endphp
                                                <th class="d-none d-xl-table-cell text-center">{{ $monthName }}</th>
                                                <th class="d-table-cell d-xl-none text-center">{{ $shortMonthName }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->name_category }}</td>
                                                @for ($month = 1; $month <= 12; $month++)
                                                    @php
                                                        $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                    @endphp
                                                    <td class="text-center">
                                                        {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                        <tr class="table-success">
                                            <td>Łącznie (PLN):</td>
                                            @for ($month = 1; $month <= 12; $month++)
                                                @php
                                                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                @endphp
                                                <td class="text-center">
                                                    {{ $monthlyTotalExpenses[$year][$monthKey] ?? '-' }}
                                                </td>
                                            @endfor
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCharts_{{ $year }}">
                        <button class="accordion-button collapsed text-bg-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCharts_{{ $year }}"
                            aria-expanded="false" aria-controls="collapseCharts_{{ $year }}">
                            <h5><b>Wykresy - zobrazowanie rocznych wydatków pod względem kategorii oraz podkategorii.<b>
                            </h5>
                        </button>
                    </h2>
                    <div id="collapseCharts_{{ $year }}" class="accordion-collapse collapse"
                        aria-labelledby="headingCharts_{{ $year }}">
                        <div class="accordion-body">
                            <canvas id="categoryYearlyChart_{{ $year }}"></canvas>
                            <div class="row">
                                @foreach ($subcategoryYearlyTotal[$year] ?? [] as $categoryName => $subcategories)
                                    @if ($categoryName !== 'Plany oszczędnościowe')
                                        @php
                                            // Formatowanie nazwy kategorii bez spacji
                                            $formattedCategoryName = str_replace(' ', '_', $categoryName);
                                        @endphp

                                        <script>
                                            var subcategoryData_{{ $year }}_{{ $formattedCategoryName }} = @json($subcategories);

                                            document.addEventListener('DOMContentLoaded', function() {
                                                var subcategoryCtx_{{ $year }}_{{ $formattedCategoryName }} = document.getElementById(
                                                    'subcategoryYearlyChart_{{ $year }}_{{ $formattedCategoryName }}');

                                                if (subcategoryCtx_{{ $year }}_{{ $formattedCategoryName }}) {
                                                    var subcategoryCtx_{{ $year }}_{{ $formattedCategoryName }} = document.getElementById(
                                                        'subcategoryYearlyChart_{{ $year }}_{{ $formattedCategoryName }}');

                                                    if (subcategoryCtx_{{ $year }}_{{ $formattedCategoryName }}) {
                                                        var subcategoryLabels_{{ $year }}_{{ $formattedCategoryName }} = Object.keys(
                                                            subcategoryData_{{ $year }}_{{ $formattedCategoryName }});
                                                        var subcategoryDataValues_{{ $year }}_{{ $formattedCategoryName }} = Object.values(
                                                            subcategoryData_{{ $year }}_{{ $formattedCategoryName }});

                                                        var subcategoryChart_{{ $year }}_{{ $formattedCategoryName }} = new Chart(
                                                            subcategoryCtx_{{ $year }}_{{ $formattedCategoryName }}, {
                                                                type: 'doughnut',
                                                                data: {
                                                                    labels: subcategoryLabels_{{ $year }}_{{ $formattedCategoryName }},
                                                                    datasets: [{
                                                                        label: 'Kwota roczna',
                                                                        data: subcategoryDataValues_{{ $year }}_{{ $formattedCategoryName }},
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
                                                    }
                                                }
                                            });
                                        </script>
                                        <div class="col-md-4 mt-2">
                                            <div class="card my-2">
                                                <div class="card-header text-bg-secondary">
                                                    <b>{{ $categoryName }} </b>
                                                </div>
                                                <div class="card-body" style="max-height: 300px">
                                                    <canvas
                                                        id="subcategoryYearlyChart_{{ $year }}_{{ $formattedCategoryName }}"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="module">
                var categoryData_{{ $year }} = @json($categoryYearlyTotal[$year] ?? []);

                document.addEventListener('DOMContentLoaded', function() {
                    var ctx_{{ $year }} = document.getElementById('categoryYearlyChart_{{ $year }}')
                        .getContext('2d');
                    var labels_{{ $year }} = Object.keys(categoryData_{{ $year }}) || [];
                    var data_{{ $year }} = Object.values(categoryData_{{ $year }}) || [];

                    var colors_{{ $year }} = [
                        'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                        'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)',
                        'rgba(75, 192, 192, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)'
                    ];

                    var chart_{{ $year }} = new Chart(ctx_{{ $year }}, {
                        type: 'bar',
                        data: {
                            labels: labels_{{ $year }},
                            datasets: [{
                                label: 'Roczne wydatki',
                                data: data_{{ $year }},
                                backgroundColor: colors_{{ $year }},
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Kwota wydatków (PLN)'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            animation: {
                                onComplete: function() {
                                    var chartInstance = this.chart;
                                    if (chartInstance) {
                                        var ctx = chartInstance.ctx;
                                        ctx.font = Chart.helpers.fontString(Chart.defaults.font.size, Chart
                                            .defaults.font.style, Chart.defaults.font.family);
                                        ctx.textAlign = 'center';

                                        this.data.datasets.forEach(function(dataset, i) {
                                            var meta = chartInstance.controller.getDatasetMeta(i);
                                            if (meta && meta.data) {
                                                meta.data.forEach(function(bar, index) {
                                                    var data = dataset.data[index];
                                                    var category = labels_{{ $year }}[
                                                        index] || '';
                                                    ctx.fillStyle = 'black';
                                                    ctx.fillText(category + ': ' + (data ? data
                                                            .toLocaleString('pl-PL', {
                                                                style: 'currency',
                                                                currency: 'PLN'
                                                            }) : 'Brak danych'), bar.x, bar
                                                        .y - 5);
                                                });
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    });

                });
            </script>
        @endforeach
    </div>

    <script>
        function generateYearlyReport() {
            // Pobierz aktualne wartości parametrów z URL
            var startYear = "{{ request()->input('start_year', now()->year) }}";
            var endYear = "{{ request()->input('end_year', now()->year) }}";
            var categories = {!! json_encode(request()->input('categories', [])) !!};

            // Ustaw wartości parametrów w formularzu
            document.getElementById('start_year').value = startYear;
            document.getElementById('end_year').value = endYear;

            // Ustaw wartości kategorii w formularzu
            categories.forEach(function(category) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'categories[]';
                input.value = category;
                document.getElementById('yearlyReportForm').appendChild(input);
            });

            // Wyślij formularz
            document.getElementById('yearlyReportForm').submit();
        }
    </script>
@endsection
