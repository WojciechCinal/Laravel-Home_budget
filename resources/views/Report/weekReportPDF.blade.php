<!DOCTYPE html>
<html lang="pl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weekly Report PDF</title>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            font-size: 14px;
        }

        h1 {
            color: #212121;
            font-size: 24px;
            text-align: center;
            font-style: italic;
            margin-bottom: 30px;
        }

        h2 {
            color: #333;
            font-size: 18px;
            font-style: italic;
        }

        h3 {
            color: #212121;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;

        }

        li {
            margin-left: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        th {
            background-color: #2c2c2c;
            color: #ffffff;
            border-right: 1px solid #dfdfdf;
            text-align: center;
        }

        .gray {
            background-color: #e7e7e7;
        }

        th:last-child {
            background-color: #2c2c2c;
            color: #f8f8f8;
            border-right: 1px solid #333;
        }

        th:first-child {
            font-weight: bold;
        }

        .tfoot {
            background-color: #9df08ce8;
            font-weight: bold;
        }

        .catName {
            font-style: italic;
            text-align: left;
        }
    </style>
</head>

<body>
    @foreach ($transactionsByWeek as $week => $transactionsInWeek)
        @php

            $weekTotal = 0;
            foreach ($transactionsInWeek as $transaction) {
                $weekTotal += $transaction->amount_transaction;
            }
        @endphp
        @php
            $carbonWeek = \Carbon\Carbon::parse($week);
            $year = $carbonWeek->format('Y');
            $weekNumber = $carbonWeek->format('W');
            $startDay = $carbonWeek->startOfWeek()->translatedFormat('d M');
            $endDay = $carbonWeek->endOfWeek()->translatedFormat('d M');
        @endphp
        <h1>{{ $year }}r. tydz. {{ $weekNumber }} - zestawienie tygodniowe.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">

        <h3>Tydz. {{ $weekNumber }} {{ $year }} - zestawienie kategorii i podkategorii.</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Kategoria</th>
                    <th>Suma wydatków</th>
                    <th>Podkategorie</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weekTotalsCat[$week] ?? [] as $categoryName => $categorySum)
                    @php
                        $subCategories = $weekTotalsSubCat[$week][$categoryName] ?? [];
                    @endphp

                    @if ($loop->even)
                        <tr class="gray">
                            <td class="catName">{{ $categoryName }}</td>
                            <td style="text-align: center;">
                                <b>{{ $categorySum }} PLN</b>
                            </td>
                            <td>
                                @foreach ($subCategories as $subCategory => $amount)
                                    <li>{{ $subCategory }}: <b>{{ $amount }} PLN</b></li>
                                @endforeach
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="catName">{{ $categoryName }}</td>
                            <td style="text-align: center;">
                                <b>{{ $categorySum }} PLN</b>
                            </td>
                            <td>
                                @foreach ($subCategories as $subCategory => $amount)
                                    <li>{{ $subCategory }}: <b>{{ $amount }} PLN</b></li>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr class="tfoot" style="font-size: 16px;">
                    <td>Wydatki tygodniowe:</td>
                    <td style="text-align: center; font-style: italic;">
                        {{$weekTotal}} PLN
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div class="page-break"></div>
        <h1>{{ $year }}r. tydz. {{ $weekNumber }} - zestawienie tygodniowe.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">

        <h3>Tydz. {{ $weekNumber }} {{ $year }} - zestawienie kategorii i podkategorii.</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tydz. {{ $weekNumber }}: {{$startDay}} - {{$endDay}}</th>
                    @foreach ($transactionsByDay[$week]->keys() as $day)
                        @php
                        $dayName = \Carbon\Carbon::parse($day)->format('d');
                        @endphp
                        <th>{{ $dayName }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    @if ($loop->even)
                        <tr class="gray">
                            <td class="catName">{{ $category->name_category }}</td>
                            @foreach ($transactionsByDay[$week] as $day => $transactionsInDay)
                                @if ($transactionsInDay->where('category.name_category', $category->name_category)->sum('amount_transaction') == 0)
                                    <td style="text-align: center;">
                                        -
                                    </td>
                                @else
                                    <td style="text-align: center;">
                                        {{ $transactionsInDay->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                            0 }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @else
                        <tr>
                            <td class="catName">{{ $category->name_category }}</td>
                            @foreach ($transactionsByDay[$week] as $day => $transactionsInDay)
                                @if ($transactionsInDay->where('category.name_category', $category->name_category)->sum('amount_transaction') == 0)
                                    <td style="text-align: center;">
                                        -
                                    </td>
                                @else
                                    <td style="text-align: center;">
                                        {{ $transactionsInDay->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                            0 }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endforeach
                <tr class="tfoot">
                    <td>Łącznie (PLN):</td>
                    @foreach ($dayTotals[$week] as $dayTotal)
                        <td style="text-align: center;">{{ $dayTotal }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>

</html>
