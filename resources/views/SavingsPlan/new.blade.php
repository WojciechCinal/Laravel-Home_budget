@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Dodaj nowy cel oszczędnościowy
                        </div>
                        <div>
                            <a href="{{ route('shopping-lists.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('savings-plans.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name_savings_plan" class="form-label">Nazwa celu
                                    oszczędnościowego</label>
                                <input type="text" class="form-control" id="name_savings_plan" name="name_savings_plan">
                            </div>

                            <div class="mb-3">
                                <label for="goal_savings_plan" class="form-label">Cel oszczędnościowy
                                    (PLN)</label>
                                <input type="numeric" class="form-control" id="goal_savings_plan" name="goal_savings_plan">
                            </div>

                            <div class="mb-3">
                                <label for="end_date_savings_plan" class="form-label">Planowana data
                                    zakończenia</label>
                                <input type="date" class="form-control" id="end_date_savings_plan"
                                    name="end_date_savings_plan">
                            </div>

                            <div class="mb-3">
                                <label for="priority_id" class="form-label">Priorytet</label>
                                <select class="form-select" id="priority_id" name="priority_id">
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->id_priority }}">{{ $priority->name_priority }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Dodaj</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
