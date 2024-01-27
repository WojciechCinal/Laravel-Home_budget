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
                        <form action="{{ route('savings-plans.store') }}" method="POST">
                            @csrf

                            <div class="form-floating mb-2">
                                <input type="text" class="form-control @error('name_savings_plan') is-invalid @enderror"
                                    id="name_savings_plan" name="name_savings_plan" placeholder=""
                                    value="{{ old('name_savings_plan') }}" required>
                                <label for="name_savings_plan" class="ms-2">Nazwa celu oszczędnościowego</label>
                                @error('name_savings_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-2">
                                <div class="form-floating col-4">
                                    <input type="number"
                                        class="form-control @error('goal_savings_plan') is-invalid @enderror"
                                        id="goal_savings_plan" name="goal_savings_plan" placeholder=""
                                        value="{{ old('goal_savings_plan') }}" required>
                                    <label for="goal_savings_plan" class="ms-2">Cel oszczędnościowy (PLN)</label>
                                    @error('goal_savings_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating col-4">
                                    <input type="date"
                                        class="form-control @error('end_date_savings_plan') is-invalid @enderror"
                                        id="end_date_savings_plan" name="end_date_savings_plan" required
                                        min="{{ date('Y-m-d') }}" value="{{ old('end_date_savings_plan') }}">
                                    <label for="end_date_savings_plan" class="ms-2">Planowana data zakończenia</label>
                                    @error('end_date_savings_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating col-4">
                                    <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id"
                                        name="priority_id" required>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->id_priority }}"
                                                {{ old('priority_id') == $priority->id_priority ? 'selected' : '' }}>
                                                {{ $priority->name_priority }}</option>
                                        @endforeach
                                    </select>
                                    <label for="priority_id" class="ms-2">Priorytet</label>
                                    @error('priority_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz plan oszczędnościowy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
