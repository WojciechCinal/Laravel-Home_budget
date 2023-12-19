@extends('layouts.app')

@section('content')
@include('Category.modalSubCat')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <span class="align-middle"> Kategoria: {{ $category->name_category }}</span>
                        <div class="ms-auto">
                            <a href="{{ route('subCategory.new', ['id' => $category->id_category]) }}"
                                class="btn btn-success btn-sm mx-2">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                                podkategoria
                            </a>
                            <a href="{{ route('category.list') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-4 col-sm-4">Nazwa podkategorii</th>
                                    <th class="col-4 col-sm-4 text-center">Opcje</th>
                                    <th class="col-4 col-sm-4 text-center">Czy aktywna?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subCategories as $subCategory)
                                    <tr>
                                        <td class="col-4 col-sm-4 align-middle">{{ $subCategory->name_subCategory }}</td>
                                        <td class="col-4 col-sm-4 text-center">
                                            <button class="btn btn-warning btn-sm edit-subcategory"
                                                data-id="{{ $subCategory->id_subCategory }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>
                                        </td>
                                        <td class="col-4 col-sm-4 text-center">
                                            <input type="checkbox" class="form-check-input subcategory-status"
                                                data-id="{{ $subCategory->id_subCategory }}"
                                                {{ $subCategory->is_active ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
