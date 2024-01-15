<!DOCTYPE html>
<html lang="pl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Monthly Report PDF</title>

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

        .category-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .category-table th,
        .category-table td {
            text-align: left;
            padding: 8px;
        }

        .category-table th {
            background-color: #4a4a4a;
            color: #ffffff;
        }

        .gray {
            background-color: #e7e7e7;
            /* Szare tło dla parzystych */
        }

        .month-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .month-table th,
        .month-table td {
            text-align: center;
            padding: 8px;
        }

        .month-table th {
            background-color: #2c2c2c;
            color: #f8f8f8;
            border-right: 1px solid #e5e5e5;
        }

        .month-table th:last-child {
            background-color: #2c2c2c;
            color: #f8f8f8;
            border-right: 1px solid #333;
        }

        .month-table th:first-child {
            font-weight: bold;
        }

        .tfoot {
            background-color: #9df08ce8;
            font-weight: bold;
        }

        .month-table .catName {
            font-style: italic;
            text-align: left;
        }
    </style>
</head>

<body>
    @php
        $year = request('selected_year');
    @endphp
    @foreach ($transactionsByMonth as $month => $transactionsInMonth)
        @php
            $monthName = \Carbon\Carbon::createFromFormat('m', $month)
                ->locale('pl')
                ->isoFormat('MMMM');

            $monthTotal = 0;
            foreach ($transactionsInMonth as $transaction) {
                $monthTotal += $transaction->amount_transaction;
            }
        @endphp
        <h1>{{ $year }} r. - zestawienie miesięczne.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">

        <h3>{{ $monthName }} {{ $year }} - zestawienie kategorii i podkategorii.</h3>
        <table class="category-table">
            <thead>
                <tr>
                    <th>Kategoria</th>
                    <th>Suma wydatków</th>
                    <th>Podkategorie</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthTotalsCat[$month] ?? [] as $categoryName => $categorySum)
                    @php
                        $subCategories = $monthTotalsSubCat[$month][$categoryName] ?? [];
                    @endphp

                    @if ($loop->even)
                        <tr class="gray">
                            <td>{{ $categoryName }}</td>
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
                            <td>{{ $categoryName }}</td>
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
                    <td>Wydatki miesięczne:</td>
                    <td style="text-align: center; font-style: italic;">
                        {{ $monthTotal }} PLN
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div class="page-break"></div>
        <h1>{{ $year }} r. - zestawienie miesięczne.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">

        <h3>{{ $monthName }} {{ $year }} - tygodniowe zestawienie kategorii.</h3>
        <table class="month-table">
            <thead>
                <tr>
                    <th>{{ $monthName }}
                        {{ $year }}</th>
                    @foreach ($transactionsByWeek[$month] as $week)
                        <th>{{ $week['week_dates'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    @if ($loop->even)
                        <tr class="gray">
                            <td class="catName">{{ $category->name_category }}</td>
                            @foreach ($transactionsByWeek[$month] as $week => $transactionsInWeek)
                                @if ($transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') == 0)
                                    <td>
                                        -
                                    </td>
                                @else
                                    <td>
                                        {{ $transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                            0 }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @else
                        <tr>
                            <td class="catName">{{ $category->name_category }}</td>
                            @foreach ($transactionsByWeek[$month] as $week => $transactionsInWeek)
                                @if ($transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') == 0)
                                    <td>
                                        -
                                    </td>
                                @else
                                    <td>
                                        {{ $transactionsInWeek->where('category.name_category', $category->name_category)->sum('amount_transaction') ??
                                            0 }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endforeach
                <tr class="tfoot">
                    <td>Łącznie (PLN):</td>
                    @foreach ($weekTotals[$month] as $weekTotal)
                        <td>{{ $weekTotal }}</td>
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
