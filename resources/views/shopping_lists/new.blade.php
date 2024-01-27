@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div id="messages">@include('layouts.messages')</div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mt-3">Dodaj nową listę zakupów.</h4>
                        </div>
                        <div>
                            <a href="{{ route('shopping-lists.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('shopping-lists.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title_shopping_list" class="form-label">Tytuł</label>
                                <input type="text"
                                    class="form-control @error('title_shopping_list') is-invalid @enderror"
                                    id="title_shopping_list" name="title_shopping_list"
                                    value="{{ old('title_shopping_list') }}" required>
                                @error('title_shopping_list')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description_shopping_list" class="form-label">Opis</label>
                                <textarea class="form-control @error('description_shopping_list') is-invalid @enderror" id="description_shopping_list"
                                    name="description_shopping_list">{{ old('description_shopping_list') }}</textarea>
                                @error('description_shopping_list')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Utwórz listę zakupów</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
