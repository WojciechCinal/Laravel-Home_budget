@extends('layouts.app')

@section('content')
    @include('Transaction.modal')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Historia transakcji.</h1>
            <a href="{{ route('transactions.create') }}" class="btn btn-success btn-sm mx-2">
                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa transakcja
            </a>
        </div>
        <div id="messages">@include('layouts.messages')</div>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Kwota</th>
                        <th>Data</th>
                        <th>Kategoria</th>
                        <th>Podkategoria</th>
                        <th>Opcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->name_transaction }}</td>
                            <td>{{ $transaction->amount_transaction }}</td>
                            <td>{{ $transaction->date_transaction }}</td>
                            <td>{{ $transaction->category->name_category }}</td>
                            <td>
                                @if ($transaction->id_subCategory == null)
                                    -
                                @else
                                    {{ $transaction->subcategory->name_subCategory }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('transactions.edit', $transaction->id_transaction) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i> Edytuj
                                </a>
                                <button type="button" class="btn btn-danger deleteButton"
                                    data-transaction-id="{{ $transaction->id_transaction }}">
                                    <i class="bi bi-trash3-fill align-middle" style="font-size: 1rem;"></i>
                                    Usuń
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="nav justify-content-center mt-2">
            {{ $transactions->links() }}
        </div>
        <div class="position-fixed bottom-1 start-0 p-3 mb-4">
            <button type="button" class="btn btn-info" data-bs-toggle="popover" data-bs-placement="right"
                title="Informacje o wydatkach" data-bs-html="true"
                data-bs-content="Wydatki w bieżącym miesiącu: {{ $expensesThisMonth }} zł <br> Budżet miesięczny: {{ $monthlyBudget }} zł <br> Pozostało: {{ $remainingFunds }} zł">
                <i class="bi bi-journal-bookmark-fill align-middle" style="font-size: 2rem;"></i>
            </button>
        </div>
    </div>
@endsection
