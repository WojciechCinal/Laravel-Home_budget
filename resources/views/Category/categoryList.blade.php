@extends('layouts.app')

@section('content')
    @include('Category.modalCat')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages"></div>
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <span class="align-middle">{{ __('Moje kategorie') }}</span>
                        <div class="ms-auto">
                            <a href="{{ route('create.category') }}" class="btn btn-success btn-sm mx-2">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                                kategoria
                            </a>
                            <a href="{{ route('category.archiveList') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-archive-fill align-middle" style="font-size: 1rem;"></i> Archiwum
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('layouts.messages')

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-3 col-sm-3">Nazwa kategorii</th>
                                    <th class="col-3 col-sm-3 text-center">Aktywne podkategorie</th>
                                    <th class="col-6 col-sm-6 text-center">Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="col-6 col-sm-4 align-middle">{{ $category->name_category }}</td>
                                        <td class="col-6 col-sm-2 text-center">
                                            {{ $category->activeSubcategoriesCount() }}/{{ $category->subcategories()->count() }}
                                        </td>
                                        <td class="col-6 col-sm-4 text-center">
                                            <button class="btn btn-warning btn-sm edit-category"
                                                data-id="{{ $category->id_category }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>

                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('subCategory.list', ['id' => $category->id_category]) }}">
                                                <i class="bi bi-list-task  align-middle" style="font-size: 1rem;"></i> Pokaż
                                                podkategorie</a>

                                            <a href="{{ route('archive.category', ['id' => $category->id_category]) }}"
                                                class="btn btn-secondary btn-sm">
                                                <i class="bi bi-archive align-middle" style="font-size: 1rem;"></i>
                                                Archiwizuj
                                            </a>

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
