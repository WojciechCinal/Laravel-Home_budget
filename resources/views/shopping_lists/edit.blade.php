@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div id="messages">@include('layouts.messages')</div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Edytuj listę zakupów</h4>
                        </div>
                        <div>
                            <a href="{{ route('shopping-lists.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('shopping-lists.update', ['id' => $shoppingList->id_shopping_list]) }}"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label">Tytuł</label>
                                <input type="text" class="form-control" id="title"
                                    name="title" value="{{ $shoppingList->title_shopping_list }}" required minlength="3" maxlength="150"
                                    oninput="this.value = this.value.trim()">
                                <div class="invalid-feedback">Podaj tytuł listy zakupów (od 3 do 150 znaków).</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Opis</label>
                                <textarea class="form-control" id="description" name="description"
                                    required minlength="3" maxlength="2000">{{ $shoppingList->description_shopping_list }}</textarea>
                                <div class="invalid-feedback">Wpisz produkty na listę zakupów (od 3 do 2000 znaków).</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
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
@endsection
