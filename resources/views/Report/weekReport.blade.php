@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="messages">
            @include('layouts.messages')
        </div>

        @foreach ($transactionsByDay as $week => $dailyTransactions)
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Kategoria</th>
                    @foreach ($dailyTransactions as $day => $dayTransactions)
                        <th class="text-center">{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name_category }}</td>
                        @foreach ($dailyTransactions as $day => $dayTransactions)
                            @php
                                $categoryTotal = 0;
                                foreach ($dayTransactions as $transaction) {
                                    if ($transaction->category->name_category === $category->name_category) {
                                        $categoryTotal += $transaction->amount;
                                    }
                                }
                            @endphp
                            <td class="text-center">{{ $categoryTotal }}</td>
                        @endforeach
                    </tr>
                @endforeach
                <tr class="table-success">
                    <td>Łącznie (PLN):</td>
                    @foreach ($dayTotals[$week] as $total)
                        <td class="text-center">{{ $total }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach
    </div>
@endsection
