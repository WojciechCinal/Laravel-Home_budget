@extends('layouts.app')

@section('content')
    @include('shopping_lists.modal')
    <div class="container">
        <div id="messages">@include('layouts.messages')</div>
        <div class="d-flex justify-content-between align-items-center">
            <h3>Listy zakupów.</h3>
            <a href="{{ route('shopping-lists.new') }}" class="btn btn-success btn-sm mx-2">
                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                lista
            </a>
        </div>
        <div class="row">
            @foreach ($shoppingLists as $list)
                <div class="col-xl-4 mt-4">
                    <div class="card bg-warning h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><b>{{ $list->title_shopping_list }}</b></h5>
                            <button class="btn btn-outline-dark zoomButton" data-list-id="{{ $list->id_shopping_list }}"><i
                                    class="bi bi-arrows-fullscreen" style="font-size: 0.8rem;"></i></button>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                {{ \Illuminate\Support\Str::limit($list->description_shopping_list, 180) }}
                            </p>
                        </div>
                        <div class="d-none">
                            <p>{{ $list->description_shopping_list }}</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted mr-auto">{{ $list->formatted_updated_at }}</small>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('shopping-lists.edit', ['id' => $list->id_shopping_list]) }}"
                                        class="btn btn-primary me-2"><i class="bi bi-pencil-square align-middle"
                                            style="font-size: 1rem;"></i> Edytuj</a>
                                    <button class="btn btn-danger deleteButton"
                                        data-list-id="{{ $list->id_shopping_list }}"><i class="bi bi-trash3-fill align-middle"
                                        style="font-size: 1rem;"></i> Usuń</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="nav justify-content-center mt-2">
            {{ $shoppingLists->links() }}
        </div>
    </div>
@endsection
