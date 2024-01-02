@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Edytuj plan oszczędnościowy
                        </div>
                        <div>
                            <a href="{{ route('savings-plans.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('savings-plans.update', ['id' => $savingsPlan->id_savings_plan]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Nazwa planu</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $savingsPlan->name_savings_plan }}">
                            </div>
                            <div class="form-group">
                                <label for="priority">Priorytet</label>
                                <select class="form-control" id="priority" name="priority">
                                    <option value="1" {{ $savingsPlan->id_priority == 1 ? 'selected' : '' }}>Bardzo wysoki</option>
                                    <option value="2" {{ $savingsPlan->id_priority == 2 ? 'selected' : '' }}>Wysoki</option>
                                    <option value="3" {{ $savingsPlan->id_priority == 3 ? 'selected' : '' }}>Średni</option>
                                    <option value="4" {{ $savingsPlan->id_priority == 4 ? 'selected' : '' }}>Mały</option>
                                    <option value="5" {{ $savingsPlan->id_priority == 5 ? 'selected' : '' }}>Brak</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="end_date">Planowana data zakończenia</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $savingsPlan->end_date_savings_plan }}">
                            </div>
                            <div class="form-group">
                                <label for="goal">Cel oszczędnościowy</label>
                                <input type="text" class="form-control" id="goal" name="goal"
                                    value="{{ $savingsPlan->goal_savings_plan }}">
                            </div>
                            <!-- Dodaj inne pola do edycji -->

                            <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
