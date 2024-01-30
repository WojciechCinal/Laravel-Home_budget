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
                        <form method="POST" action="{{ route('profile.changePassword') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Aktualne hasło: </label>
                                <input class="form-control" type="password" id="current_password" name="current_password"
                                    autofocus="">
                                @error('current_password')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nowe hasło: </label>
                                <input class="form-control" type="password" id="new_password" name="new_password">
                                @error('new_password')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Potwierdź nowe hasło: </label>
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password">
                                @error('confirm_password')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid fw-bold">
                                <button type="submit" class="btn btn-primary">
                                    Zmień hasło
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
