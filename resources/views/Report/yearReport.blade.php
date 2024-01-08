@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach ($yearlyExpenses as $year => $yearData)
            <div class="card my-2">
                <div class="card-header">
                    {{ $year }}
                </div>
                <div class="card-body">
                    <!-- Tabela z danymi -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nazwa kategorii</th>
                                @for ($month = 1; $month <= 12; $month++)
                                    <th>{{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }}</th>
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
                                        <td>{{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}</td>
                                    @endfor
                                </tr>
                            @endforeach
                            <tr>
                                <td>Łącznie:</td>
                                @for ($month = 1; $month <= 12; $month++)
                                    @php
                                        $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                    @endphp
                                    <td>{{ $monthlyTotalExpenses[$year][$monthKey] ?? '-' }} PLN</td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <canvas id="categoryYearlyChart_{{ $year }}"></canvas>
                </div>
            </div>

            <script type="module">
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx_{{ $year }} = document.getElementById('categoryYearlyChart_{{ $year }}')
                        .getContext('2d');
                    var labels_{{ $year }} = @json($categories->pluck('name_category')); // Pobierz nazwy kategorii
                    var data_{{ $year }} =
                        @json($categoryYearlyTotal[$year] ?? []); // Pobierz dane wydatków na kategorie w roku

                    // var totalExpenses_{{ $year }} = Object.values(data_{{ $year }}).reduce((a, b) => a +
                    //     b, 0); // Całkowite wydatki w roku

                    var datasetData_{{ $year }} = labels_{{ $year }}.map(function(label) {
                        var categoryExpense = data_{{ $year }}[label] || 0;
                        return categoryExpense;
                    });

                    var colors_{{ $year }} = [
                        'rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                        'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)',
                        'rgba(75, 192, 192, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)'
                    ];

                    var totalExpenses_{{ $year }} = Object.values(data_{{ $year }}).reduce((a, b) => a +
                        b, 0);

                    var chart_{{ $year }} = new Chart(ctx_{{ $year }}, {
                        type: 'bar',
                        data: {
                            labels: labels_{{ $year }},
                            datasets: [{
                                label: 'Łącznie',
                                data: datasetData_{{ $year }},
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

                                        this.data.datasets.forEach(function(dataset, i) {
                                            var meta = chartInstance.controller.getDatasetMeta(i);
                                            if (meta && meta.data) {
                                                meta.data.forEach(function(bar, index) {
                                                    var data = dataset.data[index];
                                                    var percentage = ((data /
                                                        {{ $monthlyTotalExpenses[$year][$monthKey] ?? 0 }}
                                                        ) * 100).toFixed(2) + '%';
                                                    ctx.fillStyle = 'black';
                                                    ctx.fillText(data.toLocaleString('pl-PL', {
                                                            style: 'currency',
                                                            currency: 'PLN'
                                                        }) + ' (' + percentage + ')', bar.x,
                                                        bar.y - 5);
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
@endsection
