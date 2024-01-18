@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-2"> Dodaj nową podkategorię do:<b> {{ $category->name_category }}</b></h4>
                        </div>
                        <div>
                            <a href="{{ route('subCategory.list', ['id' => $category->id_category]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('subCategory.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id_category }}">

                            <div class="mb-3">
                                <label for="name_subCategory" class="form-label">Nazwa podkategorii</label>
                                <input type="text" class="form-control" id="name_subCategory"
                                    name="name_subCategory" required minlength="3" maxlength="60"
                                    oninput="this.value = this.value.trim()">
                                <div class="invalid-feedback">Podaj nazwę nowej podkategorii (od 3 do 60 znaków).</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz podkategorię</button>
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
