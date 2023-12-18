@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Dodaj nową listę zakupów
                        </div>
                        <div>
                            <a href="{{ route('shopping-lists.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('shopping-lists.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="title_shopping_list" class="form-label">Tytuł</label>
                                <input type="text" class="form-control" id="title_shopping_list" name="title_shopping_list">
                            </div>

                            <div class="mb-3">
                                <label for="description_shopping_list" class="form-label">Opis</label>
                                <textarea class="form-control" id="description_shopping_list" name="description_shopping_list"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Dodaj</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
