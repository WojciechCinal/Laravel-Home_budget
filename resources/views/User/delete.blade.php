@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Usuwanie profilu') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.deleteProfile', ['id' => auth()->user()->id_user]) }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('E-mail') }}</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Hasło') }}</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger">{{ __('Usuń profil') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
