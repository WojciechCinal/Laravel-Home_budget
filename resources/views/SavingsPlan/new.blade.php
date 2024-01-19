@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Dodaj nowy cel oszczędnościowy</h4>
                        </div>
                        <div>
                            <a href="{{ route('savings-plans.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('savings-plans.store') }}" method="POST" class="needs-validation"
                            novalidate>
                            @csrf

                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="name_savings_plan" name="name_savings_plan"
                                    placeholder="" required pattern=".{3,100}" oninput="this.value = this.value.trim()">
                                <label for="name_savings_plans" class="ms-2">Nazwa celu oszczędnościowego</label>
                                <div class="invalid-feedback">Podaj nazwę celu oszczędnościowego (od 3 do 100 znaków).</div>
                            </div>

                            <div class="row mb-2">
                                <div class="form-floating col-4">
                                    <input type="number" class="form-control" id="goal_savings_plan"
                                        name="goal_savings_plan" placeholder="" required min="0" pattern="^\d+$" oninput="validateNumberInput(this)">
                                    <label for="goal_savings_plan" class="ms-2">Cel oszczędnościowy (PLN)</label>
                                    <div class="invalid-feedback">Podaj cel oszczędnościowy (liczba całkowita dodatnia).
                                    </div>
                                </div>

                                <div class="form-floating col-4">
                                    <input type="date" class="form-control" id="end_date_savings_plan"
                                        name="end_date_savings_plan" required min="{{ date('Y-m-d') }}">
                                    <label for="end_date_savings_plan" class="ms-2">Planowana data zakończenia</label>
                                    <div class="invalid-feedback">Podaj planowaną datę zakończenia.
                                    </div>
                                </div>

                                <div class="form-floating col-4">
                                    <select class="form-select" id="priority_id" name="priority_id" required>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->id_priority }}">{{ $priority->name_priority }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="priority_id" class="ms-2">Priorytet</label>
                                    <div class="invalid-feedback">Wybierz priorytet.</div>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz plan oszczędnościowy</button>
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
                            function validateNumberInput(input) {
                                input.value = input.value.replace(/[^\d]/g, '')
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
