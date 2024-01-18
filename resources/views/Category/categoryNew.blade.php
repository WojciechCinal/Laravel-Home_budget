@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Dodaj nową kategorię.</h4>
                        </div>
                        <div>
                            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('category.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Nazwa kategorii</label>
                                <input type="text" class="form-control" id="category_name"
                                    name="category_name" required minlength="3" maxlength="60"
                                    oninput="this.value = this.value.trim()">
                                <div class="invalid-feedback">Podaj nazwę nowej kategorii (od 3 do 60 znaków).</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz kategorię</button>
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
