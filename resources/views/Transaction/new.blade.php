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
                        <form action="{{ route('transactions.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="row mb-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name_transaction" name="name_transaction"
                                        aria-label="name_transaction" placeholder="" required pattern=".{3,100}"
                                        oninput="this.value = this.value.trim()">
                                    <label class="ms-2" for="name_transaction">Nazwa transakcji</label>
                                    <div class="invalid-feedback">Podaj nazwę transakcji (od 3 do 100 znaków).</div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="form-floating col-6">
                                    <input type="text" class="form-control" id="amount_transaction"
                                        name="amount_transaction" aria-label="amount_transaction" placeholder="" required
                                        pattern="^\d+(\.\d{1,2})?$">
                                    <label class="ms-2" for="amount_transaction">Kwota</label>
                                    <div class="invalid-feedback">Podaj poprawny format kwoty (np. 35.99).</div>
                                </div>

                                <div class="form-floating col-6">
                                    <input type="date" class="form-control" id="date_transaction" name="date_transaction"
                                        required>
                                    <label class="ms-2" for="date_transaction">Data</label>
                                    <div class="invalid-feedback">Podaj datę.</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-floating col-6">
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Kategoria</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id_category }}">{{ $category->name_category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="ms-2" for="category_id">Wybierz kategorię</label>
                                    <div class="invalid-feedback">Podaj kategorię.</div>
                                </div>
                                <div class="form-floating col-6">
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="" selected disabled>Podkategoria</option>
                                    </select>
                                    <label class="ms-2" for="subcategory_id">Wybierz podkategorię (opcjonalne)</label>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz transakcję</button>
                            </div>
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
