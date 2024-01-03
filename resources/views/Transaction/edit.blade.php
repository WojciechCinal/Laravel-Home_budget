@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Edytuj transakcję
                        </div>
                        <div>
                            <a href="{{ route('transactions.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('transactions.update', $transaction->id_transaction) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name_transaction">Nazwa</label>
                                <input type="text" class="form-control" id="name_transaction" name="name_transaction"
                                    value="{{ $transaction->name_transaction }}">
                            </div>
                            <div class="form-group">
                                <label for="amount_transaction">Kwota</label>
                                <input type="text" class="form-control" id="amount_transaction" name="amount_transaction"
                                    pattern="^\d+(\.\d{1,2})?$" title="Np. 34.99"
                                    value="{{ $transaction->amount_transaction }}">
                            </div>

                            <div class="form-group">
                                <label for="date_transaction">Data</label>
                                <input type="date" class="form-control" id="date_transaction" name="date_transaction"
                                    value="{{ $transaction->date_transaction }}">
                            </div>
                            <div class="form-group">
                                <label for="category_id">Kategoria</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id_category }}"
                                            @if ($category->id_category === $transaction->id_category) selected @endif>
                                            {{ $category->name_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="subcategory_id">Podkategoria</label>
                                <select class="form-control" id="subcategory_id" name="subcategory_id">
                                    <option value="">Brak</option>
                                    @foreach ($subcategoriesByCategory[$transaction->id_category] as $subcategory)
                                        <option value="{{ $subcategory->id_subCategory }}"
                                            @if ($subcategory->id_subCategory === $transaction->id_subCategory) selected @endif>
                                            {{ $subcategory->name_subCategory }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
