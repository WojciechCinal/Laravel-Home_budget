@extends('layouts.app')

@section('content')
    @include('SavingsPlan.modal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="messages">@include('layouts.messages')</div>
                Tutaj będą plany oszczędnościowe!
                <div class="row">
                    @foreach ($savingsPlans as $savingsPlan)
                        <div class="col-lg-6 mt-4" id="SPlan">
                            <div class="card border-dark">
                                <div class="card-header text-bg-secondary text-light">
                                    <h5 class="card-title" style="font-weight: 800; text-align: center;">
                                        {{ $savingsPlan->name_savings_plan }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="card-text">
                                        <table class="table">
                                            <tr class="table-secondary">
                                                <th>Data rozpoczęcia:</th>
                                                <td style="text-align: right;"> {{ $savingsPlan->formatted_created_at }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Priorytet:</th>
                                                <td style="text-align: right;">{{ $savingsPlan->priority->name_priority }}
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <th>Planowana data zakończenia:</th>
                                                <td style="text-align: right;">
                                                    {{ $savingsPlan->formatted_end_date_savings_plan }}</td>
                                            </tr>
                                            <tr>
                                                <th>Kwota / cel:</th>
                                                <td style="text-align: right;">{{ $savingsPlan->amount_savings_plan }} /
                                                    {{ $savingsPlan->goal_savings_plan }} PLN </td>
                                            </tr>
                                        </table>
                                        @if ($savingsPlan->is_completed == 1)
                                            <div class="progress my-3">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: 100%; font-size:14px; font-weight: bold;">
                                                    Ukończony!
                                                </div>
                                            </div>
                                        @else
                                            <div class="progress my-3">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar"
                                                    style="width: {{ ($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100 }}%; font-size:14px; font-weight: bold;">
                                                    {{ number_format(($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100, 1) }}%
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('savings-plans.index', ['id' => $savingsPlan->id_savings_plan]) }}"
                                                class="btn btn-primary me-2"><i class="bi bi-pencil-square align-middle"
                                                    style="font-size: 1rem;"></i> Edytuj</a>
                                            <button class="btn btn-danger deleteButton"
                                                data-list-id="{{ $savingsPlan->id_savings_plan }}"><i
                                                    class="bi bi-trash3-fill align-middle" style="font-size: 1rem;"></i>
                                                Usuń</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="nav justify-content-center mt-2">
                    {{ $savingsPlans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
