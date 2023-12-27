@extends('layouts.app')

@section('content')
    @include('shopping_lists.modal')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Moje listy zakupów.</h1>
            <a href="{{ route('shopping-lists.new') }}" class="btn btn-success btn-sm mx-2">
                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                lista
            </a>
        </div>
        <div id="messages">@include('layouts.messages')</div>
        <div class="row">
            @foreach ($shoppingLists as $list)
                <div class="col-lg-4 mt-4">
                    <div class="card bg-warning h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><b>{{ $list->title_shopping_list }}</b></h5>
                            <small class="text-muted">{{ $list->formatted_updated_at }}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                {{ \Illuminate\Support\Str::limit($list->description_shopping_list, 210) }}
                            </p>
                        </div>
                        <div class="d-none">
                            <p>{{ $list->description_shopping_list }}</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <a href="{{ route('shopping-lists.edit', ['id' => $list->id_shopping_list]) }}"
                                    class="btn btn-primary">Edytuj</a>
                                <button class="btn btn-secondary zoomButton"
                                    data-list-id="{{ $list->id_shopping_list }}">Powiększ</button>
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
