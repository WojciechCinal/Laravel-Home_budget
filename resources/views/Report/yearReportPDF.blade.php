<!DOCTYPE html>
<html lang="pl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yearly Report PDF</title>

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
        $startYear = request('start_year');
    @endphp

    @foreach ($yearlyExpenses as $year => $yearData)
        <div>
            <h1>{{ $year }} r. - zestawienie roczne.</h1>
            <hr style="height:2px;border-width:0;color:gray;background-color:gray">

            <h3>{{ $year }} - zestawienie kategorii i podkategorii.</h3>
            <table class="category-table">
                <thead>
                    <tr>
                        <th>Kategoria</th>
                        <th>Kwota kategoria</th>
                        <th>Podkategorie</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subcategoryYearlyTotal[$year] as $category => $subcategories)
                        @if ($loop->even)
                            <tr class="gray">
                                <td>{{ $category }}</td>
                                <td style="text-align: center;"> <b>{{ array_sum($subcategories) }} PLN</b></td>
                                <td>
                                    @foreach ($subcategories as $subcategory => $total)
                                        <li>{{ $subcategory }}: <b>{{ $total }} PLN</b>
                                        </li>
                                    @endforeach
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>{{ $category }}</td>
                                <td style="text-align: center;"><b>{{ array_sum($subcategories) }} PLN</b></td>
                                <td>
                                    @foreach ($subcategories as $subcategory => $total)
                                        <li>{{ $subcategory }}: <b>{{ $total }} PLN</b>
                                        </li>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="tfoot" style="font-size: 16px;">
                        <td>
                            Wydatki roczne:
                        </td>
                        <td style="text-align: center; font-style:italic;">
                            {{ $yearlyTotal[$year] }} PLN
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="page-break"></div>
        {{-- Pierwsza tabela miesiace 1-6 --}}
        <h1>{{ $year }} r. - zestawienie roczne.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">
        <h3>{{ $year }} - I połowa roku - miesięczne zestawienie kategorii.</h3>
        <table class="month-table">
            <thead>
                <tr>
                    <th>{{ $year }} r.</th>
                    @for ($month = 1; $month <= 6; $month++)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
                            $shortMonthName = Str::limit($monthName, 3, '');
                        @endphp
                        <th>{{ $shortMonthName }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    @if ($loop->even)
                        <tr>
                            <td class="catName">{{ $category->name_category }}</td>
                            @for ($month = 1; $month <= 6; $month++)
                                @php
                                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                @endphp
                                <td>
                                    {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                                </td>
                            @endfor
                        </tr>
                    @else
                        <tr class="gray">
                            <td class="catName">{{ $category->name_category }}</td>
                            @for ($month = 1; $month <= 6; $month++)
                                @php
                                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                @endphp
                                <td>
                                    {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                                </td>
                            @endfor
                        </tr>
                    @endif
                @endforeach
                <tr class="tfoot">
                    <td>Łącznie (PLN):</td>
                    @for ($month = 1; $month <= 6; $month++)
                        @php
                            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <td>
                            {{ $monthlyTotalExpenses[$year][$monthKey] ?? '-' }}
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>
        <div class="page-break"></div>
        {{-- Druga tabela dla miesiecy 7-12 --}}
        <h1>{{ $year }} r. - zestawienie roczne.</h1>
        <hr style="height:2px;border-width:0;color:gray;background-color:gray">
        <h3>{{ $year }} - II połowa roku - miesięczne zestawienie kategorii.</h3>
        <table class="month-table">
            <thead>
                <tr>
                    <th>{{ $year }} r.</th>
                    @for ($month = 7; $month <= 12; $month++)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
                            $shortMonthName = Str::limit($monthName, 3, '');
                        @endphp
                        <th>{{ $shortMonthName }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    @if ($loop->even)
                        <tr>
                            <td class="catName">{{ $category->name_category }}</td>
                            @for ($month = 7; $month <= 12; $month++)
                                @php
                                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                @endphp
                                <td>
                                    {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                                </td>
                            @endfor
                        </tr>
                    @else
                        <tr class="gray">
                            <td class="catName">{{ $category->name_category }}</td>
                            @for ($month = 7; $month <= 12; $month++)
                                @php
                                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                                @endphp
                                <td>
                                    {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                                </td>
                            @endfor
                        </tr>
                    @endif
                @endforeach
                <tr class="tfoot">
                    <td>Łącznie (PLN):</td>
                    @for ($month = 7; $month <= 12; $month++)
                        @php
                            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <td>
                            {{ $monthlyTotalExpenses[$year][$monthKey] ?? '-' }}
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>

        {{-- Dodanie strony przed kolejnym rokiem --}}
        @if ($year != $startYear)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
