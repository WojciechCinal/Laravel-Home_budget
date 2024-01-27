@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Dodaj nową kategorię.</h4>
                        </div>
                        <div>
                            <a href="{{ route('category.list') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('category.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Nazwa kategorii</label>
                                <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name') }}" required>
                                @error('category_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz kategorię</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
