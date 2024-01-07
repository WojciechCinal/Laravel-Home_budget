@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach ($yearlyExpenses as $year => $yearData)
            <div class="card my-2">
                <div class="card-header">
                    {{ $year }}
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nazwa kategorii</th>
                                @for ($month = 1; $month <= 12; $month++)
                                    <th>{{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F')}}</th>
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
            </div>
        @endforeach


    </div>

@endsection
