@extends('layouts.app')

@section('content')
    @include('Category.modalCat')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="messages">@include('layouts.messages')</div>
                Tutaj będą plany oszczędnościowe!
                <div class="row">
                    @foreach ($savingsPlans as $savingsPlan)
                        <div class="col-lg-6 mt-4">
                            <div class="card">
                                {{ $savingsPlan->name_savings_plan }}
                                <br>
                                {{ $savingsPlan->amount_savings_plan }} /
                                {{ $savingsPlan->goal_savings_plan }}
                                <br>
                                {{ $savingsPlan->priority->name_priority }}
                                <br>

                                <div class="my-3 mx-5">
                                    @if ($savingsPlan->is_completed == 1)
                                        <div class="progress" role="progressbar" aria-label="Animated striped example"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-success" style="width: 100%; font-size:14px; font-weight: bold;">100%
                                            </div>
                                        </div>
                                    @else
                                        <div class="progress" role="progressbar" aria-label="Animated striped example"
                                            aria-valuenow="{{ ($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100 }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                style="width: {{ ($savingsPlan->amount_savings_plan / $savingsPlan->goal_savings_plan) * 100 }}%; font-size:14px; font-weight: bold; ">
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
                    {{ $savingsPlans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
