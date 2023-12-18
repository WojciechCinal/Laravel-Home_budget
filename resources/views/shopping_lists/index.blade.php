@extends('layouts.app')

@section('content')
    @include('shopping_lists.modal')
    <div class="container">
        <h1>Twoje listy zakupów</h1>
        @include('layouts.messages')
        <div id="messages"></div>
        <div class="row">
            @foreach ($shoppingLists as $list)
                <div class="col-sm-4 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $list->title_shopping_list }}</h5>
                            <small class="text-muted">{{ $list->formatted_updated_at }}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $list->description_shopping_list }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('shopping-lists.edit', ['id' => $list->id_shopping_list]) }}"
                                    class="btn btn-primary mb-0">Edytuj</a>
                                <button class="btn btn-danger deleteButton"
                                    data-list-id="{{ $list->id_shopping_list }}">Usuń</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
