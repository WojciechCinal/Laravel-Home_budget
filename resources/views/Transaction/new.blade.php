@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card mt-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Dodaj nową transakcję.</h4>
                        </div>
                        <div>
                            <a href="{{ route('transactions.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                            <div class="row mb-2">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control @error('name_transaction') is-invalid @enderror"
                                        id="name_transaction" name="name_transaction" aria-label="name_transaction"
                                        placeholder="" value="{{ old('name_transaction') }}" required>
                                    <label class="ms-2" for="name_transaction">Nazwa transakcji</label>
                                    @error('name_transaction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="form-floating col-6">
                                    <input type="text"
                                        class="form-control @error('amount_transaction') is-invalid @enderror"
                                        id="amount_transaction" name="amount_transaction" aria-label="amount_transaction"
                                        placeholder="" value="{{ old('amount_transaction') }}" required>
                                    <label class="ms-2" for="amount_transaction">Kwota</label>
                                    @error('amount_transaction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating col-6">
                                    <input type="date"
                                        class="form-control @error('date_transaction') is-invalid @enderror"
                                        id="date_transaction" name="date_transaction" required
                                        value="{{ old('date_transaction') }}">
                                    <label class="ms-2" for="date_transaction">Data</label>
                                    @error('date_transaction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-floating col-6">
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="" selected disabled>Kategoria</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id_category }}"
                                                {{ old('category_id') == $category->id_category ? 'selected' : '' }}>
                                                {{ $category->name_category }}</option>
                                        @endforeach
                                    </select>
                                    <label class="ms-2" for="category_id">Wybierz kategorię</label>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating col-6">
                                    <select class="form-select @error('subcategory_id') is-invalid @enderror"
                                        id="subcategory_id" name="subcategory_id">
                                        <option value="" selected disabled>Podkategoria</option>
                                    </select>
                                    <label class="ms-2" for="subcategory_id">Wybierz podkategorię (opcjonalne)</label>
                                    @error('subcategory_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz transakcję</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const subcategoriesByCategory = {!! json_encode($subcategoriesByCategory) !!};

        document.getElementById('category_id').addEventListener('change', function() {
            var categoryId = this.value;
            var subcategorySelect = document.getElementById('subcategory_id');
            subcategorySelect.innerHTML = '<option value="" selected disabled>Wybierz podkategorię</option>';

            if (categoryId in subcategoriesByCategory) {
                subcategoriesByCategory[categoryId].forEach(sub_categories => {
                    subcategorySelect.innerHTML += '<option value="' + sub_categories.id_subCategory +
                        '">' +
                        sub_categories.name_subCategory + '</option>';
                });
            }
        });
    </script>
@endsection
