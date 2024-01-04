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
        <!-- Formularz filtrowania, sortowania i wyszukiwania -->
        <div class="accordion" id="filterAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        SORTOWANIE I FILTROWANIE
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#filterAccordion">
                    <form action="{{ route('transactions.index') }}" method="GET" class="mb-3">
                        <div class="row mt-2 justify-content-center">
                            <!-- Przedział czasowy -->
                            <div class="col-md-2">
                                <label for="start_date"><b>Data początkowa:</b></label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    autocomplete="off" value="{{ $startDate ??now()->startOfMonth()->toDateString() }}" />

                                <label for="end_date"><b>Data końcowa:</b></label>
                                <input type="date" id="end_date" name="end_date" class="form-control" autocomplete="off"
                                    value="{{ $endDate ?? now()->toDateString() }}" />
                            </div>

                            <!-- Check boxy z nazwami kategorii -->
                            <div class="col-md-3">
                                <label><b>Kategorie:</b></label>
                                <div class="scrollable-checkboxes">
                                    @foreach ($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]"
                                                value="{{ $category->id_category }}"
                                                {{ in_array($category->id_category, $selectedCategories) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $category->name_category }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Sortowanie rosnąco/malejąco -->
                            <div class="col-md-2">
                                <label for="sort_ratio"><b>Kwota:</b></label>

                                <select name="sort_ratio" id="sort_ratio" class="form-select">
                                    <option value="" {{ request('sort_ratio') === '' ? 'selected' : '' }}>Brak
                                    </option>
                                    <option value="asc" {{ request('sort_ratio') === 'asc' ? 'selected' : '' }}>Rosnąco
                                    </option>
                                    <option value="desc" {{ request('sort_ratio') === 'desc' ? 'selected' : '' }}>Malejąco
                                    </option>
                                </select>
                            </div>

                            <!-- Pole wyszukiwania -->
                            <div class="col-md-2">
                                <label for="search"><b>Wyszukaj:</b></label>
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}">
                            </div>

                            <!-- Przycisk filtruj -->
                            <div class="col-md-2 mt-1 d-flex justify-content-end align-items-end">
                                <button type="submit" class="btn btn-primary">Filtruj</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="justify-content-center">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Kwota</th>
                        <th>Data</th>
                        <th class="d-none d-md-table-cell">Kategoria</th>
                        <th class="d-md-none">Kat.</th>
                        <th class="d-none d-md-table-cell">Podkategoria</th>
                        <th class="d-md-none">Podkat.</th>
                        <th>Edytuj</th>
                        <th>Usuń</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->name_transaction }}</td>
                            <td>{{ $transaction->amount_transaction }}</td>
                            <td>{{ $transaction->formatted_date_transaction }}</td>
                            <td class="text-wrap text-break">{{ $transaction->category->name_category }}</td>
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
                                    <i class="bi bi-pencil-square align-middle" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger deleteButton"
                                    data-transaction-id="{{ $transaction->id_transaction }}">
                                    <i class="bi bi-trash3-fill align-middle" style="font-size: 0.8rem;"></i>
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
        <div class="position-fixed bottom-0 start-0 p-3 mb-4">
            <button type="button" class="btn btn-info" data-bs-toggle="popover" data-bs-placement="right"
                title="{{ $dateNow }}" data-bs-html="true"
                data-bs-content="Wydatki w bieżącym miesiącu: {{ $expensesThisMonth }} zł <br> Budżet miesięczny: {{ $monthlyBudget }} zł <br> Pozostało: {{ $remainingFunds }} zł">
                <i class="bi bi-journal-bookmark-fill align-middle" style="font-size: 2rem;"></i>
            </button>
        </div>
    </div>
@endsection
