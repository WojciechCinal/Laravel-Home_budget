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
                            action="{{ route('savings-plans.update', ['id' => $savingsPlan->id_savings_plan]) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-floating mb-2">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $savingsPlan->name_savings_plan) }}"
                                    required>
                                <label for="name" class="ms-2">Nazwa celu oszczędnościowego</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-2">
                                <div class="form-floating col-4">
                                    <input type="number" class="form-control @error('goal') is-invalid @enderror"
                                        id="goal" name="goal"
                                        value="{{ old('goal', $savingsPlan->goal_savings_plan) }}" required>
                                    <label for="goal" class="ms-2">Cel oszczędnościowy (PLN)</label>
                                    @error('goal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating col-4">
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date"
                                        value="{{ old('end_date', $savingsPlan->end_date_savings_plan) }}" required>
                                    <label for="end_date" class="ms-2">Planowana data zakończenia</label>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating col-4">
                                    <select class="form-control @error('priority') is-invalid @enderror" id="priority"
                                        name="priority">
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
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
