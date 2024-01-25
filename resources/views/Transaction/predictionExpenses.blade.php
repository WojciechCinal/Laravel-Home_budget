@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header text-bg-secondary text-center"><h4 class="mt-2">Prognoza wydatków na: <b>{{ $title }}</b></h4></div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-dark text-center">
                                    <th scope="col">Kategoria</th>
                                    <th scope="col">Prognozowane wydatki</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($averageExpenses as $category => $averageExpense)
                                    @if ($loop->even)
                                        <tr class="table-secondary">
                                            <td>{{ $category }}</td>
                                            <td class="text-end">{{ is_numeric($averageExpense) ? number_format($averageExpense, 2) . ' PLN' : $averageExpense }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $category }}</td>
                                            <td class="text-end">{{ is_numeric($averageExpense) ? number_format($averageExpense, 2) . ' PLN' : $averageExpense }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
