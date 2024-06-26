@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3"> Dodaj nową podkategorię do:<b> {{ $category->name_category }}</b></h4>
                        </div>
                        <div>
                            <a href="{{ route('subCategory.list', ['id' => $category->id_category]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('subCategory.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id_category }}">

                            <div class="mb-3">
                                <label for="name_subCategory" class="form-label">Nazwa podkategorii</label>
                                <input type="text" class="form-control @error('name_subCategory') is-invalid @enderror"
                                    id="name_subCategory" name="name_subCategory" value="{{ old('name_subCategory') }}"
                                    required>
                                @error('name_subCategory')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz podkategorię</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
