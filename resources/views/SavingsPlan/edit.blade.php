@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Edytuj plan oszczędnościowy</h4>
                        </div>
                        <div>
                            <a href="{{ route('savings-plans.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('savings-plans.update', ['id' => $savingsPlan->id_savings_plan]) }}"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $savingsPlan->name_savings_plan }}" required pattern=".{3,100}"
                                    oninput="this.value = this.value.trim()">
                                <label for="name" class="ms-2">Nazwa celu oszczędnościowego</label>
                                <div class="invalid-feedback">Podaj nazwę celu oszczędnościowego (od 3 do 100 znaków).</div>
                            </div>

                            <div class="row mb-2">
                                <div class="form-floating col-4">
                                    <input type="number" class="form-control" id="goal" name="goal"
                                        value="{{ $savingsPlan->goal_savings_plan }}" required min="0"
                                        pattern="^\d+$" oninput="validateNumberInput(this)">
                                    <label for="goal" class="ms-2">Cel oszczędnościowy (PLN)</label>
                                    <div class="invalid-feedback">Podaj cel oszczędnościowy (liczba całkowita dodatnia).
                                    </div>
                                </div>

                                <div class="form-floating col-4">
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $savingsPlan->end_date_savings_plan }}" required>
                                    <label for="end_date" class="ms-2">Planowana data zakończenia</label>
                                    <div class="invalid-feedback">Podaj planowaną datę zakończenia.</div>
                                </div>

                                <div class="form-floating col-4">
                                    <select class="form-control" id="priority" name="priority">
                                        <option value="1" {{ $savingsPlan->id_priority == 1 ? 'selected' : '' }}>
                                            Bardzo wysoki</option>
                                        <option value="2" {{ $savingsPlan->id_priority == 2 ? 'selected' : '' }}>
                                            Wysoki</option>
                                        <option value="3" {{ $savingsPlan->id_priority == 3 ? 'selected' : '' }}>
                                            Średni</option>
                                        <option value="4" {{ $savingsPlan->id_priority == 4 ? 'selected' : '' }}>
                                            Mały</option>
                                        <option value="5" {{ $savingsPlan->id_priority == 5 ? 'selected' : '' }}>
                                            Brak</option>
                                    </select>
                                    <label for="priority" class="ms-2">Priorytet</label>
                                    <div class="invalid-feedback">Wybierz priorytet.</div>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
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
