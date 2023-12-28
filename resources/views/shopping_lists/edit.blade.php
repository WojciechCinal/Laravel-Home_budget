@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Edytuj listę zakupów
                        </div>
                        <div>
                            <a href="{{ route('shopping-lists.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('shopping-lists.update', ['id' => $shoppingList->id_shopping_list]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="title">Tytuł</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ $shoppingList->title_shopping_list }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Opis</label>
                                <textarea class="form-control" id="description" name="description" style="height: 183px">{{ $shoppingList->description_shopping_list }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Zapisz zmiany</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
