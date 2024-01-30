@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6">
                <div id="messages">
                    @include('layouts.messages')
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="mt-3">Edycja profilu</h4>
                        <div class="mt-3">Zarejestrowany: {{ auth()->user()->created_at }}</div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nazwa użytkownika: </label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', auth()->user()->name) }}" autofocus="">
                                @error('name')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email: </label>
                                <input class="form-control" type="text" id="email" name="email"
                                    value="{{ old('email', auth()->user()->email) }}" autofocus="">
                                @error('email')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="monthly_budget" class="form-label">Miesięczny przychód: </label>
                                <input class="form-control" type="text" id="monthly_budget" name="monthly_budget"
                                    value="{{ old('monthly_budget', auth()->user()->monthly_budget) }}" autofocus="">
                                @error('monthly_budget')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid fw-bold">
                                <button type="submit" class="btn btn-primary">
                                    Zaktualizuj dane
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
