@extends('layouts.app')

@section('content')
    @include('SavingsPlan.modal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="messages">@include('layouts.messages')</div>
                <div class="row">
                    @foreach ($savingsPlans as $savingsPlan)
                        <div class="col-xl-6 mt-4" id="SPlan">
                            <div class="card border-dark">
                                <div class="card-header text-bg-secondary text-light">

                                        <h5 class="card-title" style="font-weight: 800; text-align: center;">
                                            {{ $savingsPlan->name_savings_plan }}
                                        </h5>

                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Priorytet:</th>
                                            <td style="text-align: right;">
                                                {{ $savingsPlan->priority->name_priority }}
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
                                        <tr class="table-secondary">
                                            <th>Data rozpoczęcia:</th>
                                            <td style="text-align: right;"> {{ $savingsPlan->formatted_created_at }}
                                            </td>
                                        </tr>
                                        @if ($savingsPlan->months_remaining == 0)
                                            <tr class="table-danger">
                                                <th >Pozostało:</th>
                                                <td style="text-align: right; color: red; font-weight: bold;">{{ $savingsPlan->deadline }}
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <th>Proponowana wpłata miesięczna:</th>
                                                <td style="text-align: right; color: red; font-weight: bold;"> {{ $savingsPlan->monthly_deposit_needed }} PLN
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <th>Pozostało:</th>
                                                <td style="text-align: right;"> {{ $savingsPlan->months_remaining }}
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <th>Proponowana wpłata miesięczna:</th>
                                                <td style="text-align: right;"> {{ $savingsPlan->monthly_deposit_needed }} PLN
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
                <div class="position-fixed bottom-0 end-0 p-3 mb-5 me-3">
                    <a href="{{ route('savings-plans.new') }}">
                        <button class="btn btn-info rounded-circle"
                            style="font-size: 20px; height:3.2rem; width:3.2rem; font-weight:bold;">
                            +
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
