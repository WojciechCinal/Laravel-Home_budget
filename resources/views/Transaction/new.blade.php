@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="messages">@include('layouts.messages')</div>
        <h1>Nowa transakcja</h1>
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name_transaction" class="form-label">Nazwa transakcji</label>
                <input type="text" class="form-control" id="name_transaction" name="name_transaction" required>
            </div>

            <div class="mb-3">
                <label for="amount_transaction" class="form-label">Kwota</label>
                <input type="number" class="form-control" id="amount_transaction" name="amount_transaction" required>
            </div>

            <div class="mb-3">
                <label for="date_transaction" class="form-label">Data</label>
                <input type="date" class="form-control" id="date_transaction" name="date_transaction" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategoria</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="" selected disabled>Wybierz kategorię</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_category }}">{{ $category->name_category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="subcategory_id" class="form-label">Podkategoria</label>
                <select class="form-select" id="subcategory_id" name="subcategory_id">
                    <option value="" selected disabled>Wybierz podkategorię</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Zapisz transakcję</button>
        </form>
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
