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
        }

        h2 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 8px;
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

        th {
            background-color: #2c2c2c;
            color: #f8f8f8;
            border-right: 1px solid #e5e5e5;
        }

        th:last-child {
            background-color: #2c2c2c;
            color: #f8f8f8;
            border-right: 1px solid #333;
        }

        th:first-child {
            font-weight: bold;
        }

        tr:nth-child(even):not(:last-child) {
            background-color: #f2f2f2;
        }

        .tfoot {
            background-color: #9df08ce8;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .catName {
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        $startYear = request('start_year');
    @endphp

    @foreach ($yearlyExpenses as $year => $yearData)
        {{-- Wyświetlenie sumy kwot dla całego roku --}}
        <div>
            <h2>Suma kwot dla całego roku {{ $year }}:</h2>
            {{-- <p>{{ $categoryTotal[$year] ?? '0' }} PLN</p> --}}
        </div>
        {{-- Wyświetlenie rocznych wydatków względem kategorii --}}
        <div>
            <h2>Roczne wydatki względem kategorii dla roku {{ $year }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Kategoria</th>
                        <th>Kwota kategoria</th>
                        <th>Podkategorie</th>
                        <th>Kwota podkategoria</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subcategoryYearlyTotal[$year] as $category => $subcategories)
                        <tr>
                            <td rowspan="{{ count($subcategories) + 1 }}">{{ $category }}</td>
                            <td rowspan="{{ count($subcategories) + 1 }}">{{ array_sum($subcategories) }} PLN</td>
                            @foreach ($subcategories as $subcategory => $total)
                        <tr>
                            <td>{{ $subcategory }} </td>
                            <td>{{ $total }} PLN</td>
                        </tr>
                    @endforeach
                    </tr>
    @endforeach
    </tbody>
    </table>
    </div>


    <div class="page-break"></div>
    {{-- Pierwsza tabela miesiace 1-6 --}}
    <table>
        <thead>
            <tr>
                <th>{{ $year }} r.</th>
                @for ($month = 1; $month <= 6; $month++)
                    @php
                        $monthName = \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
                        $shortMonthName = Str::limit($monthName, 3, '');
                    @endphp
                    <th class="center">{{ $shortMonthName }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td class="catName">{{ $category->name_category }}</td>
                    @for ($month = 1; $month <= 6; $month++)
                        @php
                            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <td class="center">
                            {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
            <tr class="tfoot">
                <td>Łącznie (PLN):</td>
                @for ($month = 1; $month <= 6; $month++)
                    @php
                        $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <td class="center">
                        {{ $monthlyTotalExpenses[$year][$monthKey] ?? '-' }}
                    </td>
                @endfor
            </tr>
        </tbody>
    </table>
    <div class="page-break"></div>
    {{-- Druga tabela dla miesiecy 7-12 --}}
    <table>
        <thead>
            <tr>
                <th>{{ $year }} r.</th>
                @for ($month = 7; $month <= 12; $month++)
                    @php
                        $monthName = \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
                        $shortMonthName = Str::limit($monthName, 3, '');
                    @endphp
                    <th class="center">{{ $shortMonthName }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td class="catName">{{ $category->name_category }}</td>
                    @for ($month = 7; $month <= 12; $month++)
                        @php
                            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <td class="center">
                            {{ $yearlyExpenses[$year][$monthKey][$category->name_category] ?? '-' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
            <tr class="tfoot">
                <td>Łącznie (PLN):</td>
                @for ($month = 7; $month <= 12; $month++)
                    @php
                        $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <td class="center">
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
