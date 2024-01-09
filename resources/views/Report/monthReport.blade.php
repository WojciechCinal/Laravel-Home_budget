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
                                        @foreach ($transactionsByWeek[$month] as $week => $transactions)
                                            <th>{{ $week }} tydzień</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactionsInMonth->pluck('category.name_category')->unique() as $category)
                                        <tr>
                                            <td>{{ $category }}</td>
                                            @foreach ($transactionsByWeek[$month] as $week => $transactionsInWeek)
                                                <td>{{ $transactionsInWeek->where('category.name_category', $category)->sum('amount_transaction') }}
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
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
