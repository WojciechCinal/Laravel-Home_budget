@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div id="messages">@include('layouts.messages')</div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-2">Edytuj transakcję</h4>
                        </div>
                        <div>
                            <a href="{{ route('transactions.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transactions.update', $transaction->id_transaction) }}"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="name_transaction" name="name_transaction"
                                    value="{{ $transaction->name_transaction }}" required pattern=".{3,100}"
                                    oninput="this.value = this.value.trim()">
                                <label for="name_transaction" class="ms-2">Nazwa transakcji</label>
                                <div class="invalid-feedback">Podaj nazwę transakcji (od 3 do 100 znaków).</div>
                            </div>
                            <div class="row mb-2">
                                <div class="form-floating col-6">
                                    <input type="text" class="form-control" id="amount_transaction"
                                        name="amount_transaction" pattern="^\d+(\.\d{1,2})?$" title="Np. 34.99"
                                        value="{{ $transaction->amount_transaction }}" required>
                                    <label for="amount_transaction" class="ms-2">Kwota</label>
                                    <div class="invalid-feedback">Podaj poprawny format kwoty (np. 35.99).</div>
                                </div>

                                <div class="form-floating col-6">
                                    <input type="date" class="form-control" id="date_transaction" name="date_transaction"
                                        value="{{ $transaction->date_transaction }}" required>
                                    <label for="date_transaction" class="ms-2">Data</label>
                                    <div class="invalid-feedback">Podaj datę.</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-floating col-6">
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id_category }}"
                                                @if ($category->id_category === $transaction->id_category) selected @endif>
                                                {{ $category->name_category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="category_id" class="ms-2">Wybierz kategorię</label>
                                    <div class="invalid-feedback">Podaj kategorię.</div>
                                </div>
                                <div class="form-floating col-6">
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="">Brak</option>
                                        @foreach ($subcategoriesByCategory[$transaction->id_category] as $subcategory)
                                            <option value="{{ $subcategory->id_subCategory }}"
                                                @if ($subcategory->id_subCategory === $transaction->id_subCategory) selected @endif>
                                                {{ $subcategory->name_subCategory }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="subcategory_id" class="ms-2">Wybierz podkategorię (opcjonalne)</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var forms = document.querySelectorAll('.needs-validation');

                                Array.prototype.slice.call(forms).forEach(function(form) {
                                    form.addEventListener('submit', function(event) {
                                        if (!form.checkValidity()) {
                                            event.preventDefault();
                                            event.stopPropagation();
                                        }

                                        form.classList.add('was-validated');
                                    }, false);
                                });
                            });
                        </script>
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
