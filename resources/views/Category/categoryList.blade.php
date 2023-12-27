@extends('layouts.app')

@section('content')
    @include('Category.modalCat')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="messages">@include('layouts.messages')</div>
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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col">Nazwa kategorii</th>
                                    <th class="col text-center">Aktywne podkategorie</th>
                                    <th class="col text-center">Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="col align-middle">{{ $category->name_category }}</td>
                                        <td class="col align-middle text-center">
                                            {{ $category->activeSubcategoriesCount() }}/{{ $category->subcategories()->count() }}
                                        </td>
                                        <td class="col text-center">
                                            <button class="btn btn-warning btn-sm edit-category my-1"
                                                data-id="{{ $category->id_category }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>

                                            <a class="btn btn-info btn-sm my-1"
                                                href="{{ route('subCategory.list', ['id' => $category->id_category]) }}">
                                                <i class="bi bi-list-task  align-middle" style="font-size: 1rem;"></i> Pokaż
                                                podkategorie</a>

                                            <a href="{{ route('archive.category', ['id' => $category->id_category]) }}"
                                                class="btn btn-secondary btn-sm my-1 ">
                                                <i class="bi bi-archive align-middle" style="font-size: 1rem;"></i>
                                                Archiwizuj
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="nav justify-content-center">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
