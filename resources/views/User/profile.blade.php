@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="mt-3">{{ auth()->user()->name }}</h4>
                        <div class="mt-3">Zarejestrowany: {{ auth()->user()->created_at }}</div>
                    </div>
                    <div class="card-body mx-3 fs-5">
                        <div class="text-center">
                            <i class="bi bi-person-vcard-fill text-center my-0 py-0" style="font-size: 10rem;"></i>
                        </div>
                        <div class="row d-flex justify-content-between mb-3">
                            <div class="col-md-6">
                                <strong>Nazwa użytkownika:</strong>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ auth()->user()->name }}
                            </div>
                        </div>
                        <div class="row d-flex justify-content-between mb-3">
                            <div class="col-md-6">
                                <strong>Email:</strong>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ auth()->user()->email }}
                            </div>
                        </div>
                        <div class="row d-flex justify-content-between mb-3">
                            <div class="col-md-6">
                                <strong>Miesięczny przychód:</strong>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ auth()->user()->monthly_budget }} PLN
                            </div>
                        </div>
                        <div class="row d-flex justify-content-between mb-3">
                            <div class="col-md-6">
                                <strong>Rola:</strong>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ auth()->user()->role->name_role }}
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary fw-medium">Edytuj profil</a>
                            <a href="" class="btn btn-warning my-2">Zmień hasło</a>
                            <a href="" class="btn btn-danger">Usuń konto</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
