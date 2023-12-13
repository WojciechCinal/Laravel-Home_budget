@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Dodaj nową kategorię.
                        </div>
                        <div>
                            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('category.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="category_name">Nazwa kategorii:</label>
                                <input type="text" class="form-control" id="category_name" name="category_name">
                            </div>

                            <button type="submit" class="btn btn-primary">Dodaj kategorię</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
