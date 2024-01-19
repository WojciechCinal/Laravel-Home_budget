@extends('layouts.app')

@section('content')
    @include('SavingsPlan.modal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div id="messages">
                    @include('layouts.messages')
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mt-2">Plany oszczędnościowe.</h3>
                            <a href="{{ route('savings-plans.new') }}" class="btn btn-success btn-sm mx-2 btn-lg">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1.5rem;"></i> Nowy
                                plan oszczędnościowy
                            </a>
                        </div>
                        <div class="accordion mt-2" id="accordionForm">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseForm" aria-controls="collapseForm">
                                        SORTOWANIE I FILTROWANIE
                                    </button>
                                </h2>
                                <div id="collapseForm" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                    data-bs-parent="#accordionForm">
                                    <div class="accordion-body">
                                        <form action="{{ route('savings-plans.index') }}" method="GET">
                                            <div class="row justify-content-end">
                                                <div class="col-md-3 mb-3">
                                                    <label for="sort_end_date">Data zakończenia:</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="asc"
                                                            id="sort_end_date_asc" name="sort_end_date"
                                                            {{ request('sort_end_date') === 'asc' || !request()->has('sort_end_date') ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="sort_end_date_asc">Rosnąco</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="desc"
                                                            id="sort_end_date_desc" name="sort_end_date"
                                                            {{ request('sort_end_date') === 'desc' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="sort_end_date_desc">Malejąco</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="sort_priority">Priorytet:</label>
                                                    @foreach ([1, 2, 3, 4, 5] as $priority)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="{{ $priority }}"
                                                                id="sort_priority_{{ $priority }}"
                                                                name="sort_priority[]"
                                                                {{ in_array($priority, request('sort_priority', [1, 2, 3, 4, 5])) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="sort_priority_{{ $priority }}">
                                                                @if ($priority === 1)
                                                                    Bardzo wysoki
                                                                @elseif ($priority === 2)
                                                                    Wysoki
                                                                @elseif ($priority === 3)
                                                                    Średni
                                                                @elseif ($priority === 4)
                                                                    Mały
                                                                @elseif ($priority === 5)
                                                                    Brak
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="sort_completed">Zakończony:</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="1"
                                                            id="sort_completed_true" name="sort_completed"
                                                            {{ request()->query('sort_completed') === '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="sort_completed_true">Tak</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="0"
                                                            id="sort_completed_false" name="sort_completed"
                                                            {{ !request()->query('sort_completed') || request()->query('sort_completed') === '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="sort_completed_false">Nie</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">Sortuj</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($savingsPlans as $savingsPlan)
                        <div class="col-xl-6 mt-4" id="SPlan-{{ $savingsPlan->id_savings_plan }}">
                            <div class="card border-dark">
                                <div
                                    class="card-header text-bg-secondary text-light d-flex justify-content-between align-items-center">
                                    <h5 class="card-title m-0 overflow-ellipsis"
                                        style="font-weight: 800;  flex: 1; white-space: nowrap;">
                                        {{ $savingsPlan->name_savings_plan }}
                                    </h5>
                                    <div class="me-2" style="font-weight: 600;">
                                        {{ number_format(($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100, 1) }}
                                        %</div>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#savingsPlanDetails{{ $savingsPlan->id_savings_plan }}">
                                        <i class="bi bi-info-square align-middle px-0 py-0" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Priorytet:</th>
                                            <td style="text-align: right;">
                                                {{ $savingsPlan->priority->name_priority }}
                                            </td>
                                        </tr>
                                        <tr class="d-none">
                                            <th>Planowana data zakończenia:</th>
                                            <td style="text-align: right;">
                                                {{ $savingsPlan->formatted_end_date_savings_plan }}</td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <th>Kwota / cel:</th>
                                            <td style="text-align: right;">
                                                {{ $savingsPlan->amount_savings_plan }} /
                                                {{ $savingsPlan->goal_savings_plan }} PLN </td>
                                        </tr>
                                        <tr class="d-none">
                                            <th>Data rozpoczęcia:</th>
                                            <td style="text-align: right;">
                                                {{ $savingsPlan->formatted_created_at }}
                                            </td>
                                        </tr>
                                        @if ($savingsPlan->months_remaining == 0)
                                            <tr class="table-danger">
                                                <th>Pozostało:</th>
                                                <td style="text-align: right; color: red; font-weight: bold;">
                                                    {{ $savingsPlan->deadline }}
                                                </td>
                                            </tr>
                                            <tr class="d-none">
                                                <th>Proponowana wpłata miesięczna:</th>
                                                <td style="text-align: right; color: red; font-weight: bold;">
                                                    {{ $savingsPlan->monthly_deposit_needed }} PLN
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Pozostało:</th>
                                                <td style="text-align: right;">
                                                    {{ $savingsPlan->months_remaining }}
                                                </td>
                                            </tr>
                                            <tr class="d-none">
                                                <th>Proponowana wpłata miesięczna:</th>
                                                <td style="text-align: right;">
                                                    {{ $savingsPlan->monthly_deposit_needed }}
                                                    PLN
                                                </td>
                                            </tr>
                                        @endif

                                    </table>
                                    @if ($savingsPlan->is_completed == 1)
                                        <div class="progress mt-3">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: 100%; font-size:14px; font-weight: bold;">
                                                Ukończony!
                                            </div>
                                        </div>
                                    @elseif ($savingsPlan->amount_savings_plan == 0)
                                        <div class="progress mt-3">
                                            <div class="progress-bar bg-secondary" role="progressbar"
                                                style="width: 100%; font-size:14px; font-weight: bold;">
                                                0 %
                                            </div>
                                        </div>
                                    @else
                                        <div class="progress mt-3">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                style="width: {{ ($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100 }}%; font-size:14px; font-weight: bold;">
                                                {{ number_format(($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100, 1) }}%
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="nav justify-content-center mt-2">
                    {{ $savingsPlans->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
