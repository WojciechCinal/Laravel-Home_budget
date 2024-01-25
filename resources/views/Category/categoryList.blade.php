@extends('layouts.app')

@section('content')
    @include('Category.modalCat')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <h3 class="mt-2">Moje kategorie</h3>
                        <div class="ms-auto">
                            <a href="{{ route('create.category') }}" class="btn btn-success btn-sm mx-2">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1.5rem;"></i> Nowa
                                kategoria
                            </a>
                            <a href="{{ route('category.archiveList') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-archive-fill align-middle" style="font-size: 1.5rem;"></i> Archiwum
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <form class="d-flex justify-content-center" role="search" action="{{ route('category.list') }}"
                            method="GET">
                            <i type="button" class="bi bi-info-square-fill mt-1 mx-3"
                                style="font-size: 2rem; color: #0dcaf0;" data-bs-container="body" data-bs-toggle="popover"
                                data-bs-placement="bottom"
                                data-bs-content="Jeśli wyszukiwana fraza nie zgadza się z nazwą kategorii, sprawdź jej podkategorie."></i>

                            <input class="form-control" type="search" name="search"
                                placeholder="Podaj nazwę kategorii lub podkategorii..." aria-label="Search"
                                style="width: 60%" value="{{ request('search') }}">
                            <button class="btn btn-success ms-2" type="submit">
                                <i class="bi bi-search align-middle" style="font-size: 1rem;"></i> Szukaj
                            </button>
                        </form>

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col">Nazwa kategorii</th>
                                    <th class="col text-center">Aktywne podkategorie</th>
                                    <th class="col text-center">Opcje</th>
                                    <th class="col text-center">Ranking</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    @if ($category->is_active == false)
                                        <tr class="table-danger">
                                        @else
                                        <tr>
                                    @endif
                                    <td class="col align-middle">
                                        {{ $category->name_category }}
                                    </td>
                                    <td class="col align-middle text-center">
                                        @if ($category->name_category == 'Brak kategorii')
                                            -
                                        @else
                                            {{ $category->activeSubcategoriesCount() }}/{{ $category->subcategories()->count() }}
                                        @endif
                                    </td>
                                    <td class="col text-center">
                                        @if ($category->name_category == 'Brak kategorii')
                                            <a href="{{ route('archive.category', ['id' => $category->id_category]) }}"
                                                class="btn btn-secondary btn-sm my-1" style="min-width: 112px">
                                                <i class="bi bi-archive align-middle" style="font-size: 1rem;"></i>
                                                Archiwizuj
                                            </a>
                                        @else
                                            <button class="btn btn-warning btn-sm edit-category my-1"
                                                style="min-width: 112px" data-id="{{ $category->id_category }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>

                                            <a class="btn btn-info btn-sm my-1"
                                                href="{{ route('subCategory.list', ['id' => $category->id_category]) }}">
                                                <i class="bi bi-list-task  align-middle" style="font-size: 1rem;"></i>
                                                Podkategorie</a>

                                            <a href="{{ route('archive.category', ['id' => $category->id_category]) }}"
                                                class="btn btn-secondary btn-sm my-1" style="min-width: 112px">
                                                <i class="bi bi-archive align-middle" style="font-size: 1rem;"></i>
                                                Archiwizuj
                                            </a>
                                        @endif
                                    </td>
                                    <td class="col text-center align-middle">
                                        @if ($category->name_start)
                                            <button class="btn btn-info btn-sm start-name">
                                                <i class="bi bi-award" style="font-size: 0.8rem"></i> </button>
                                        @else
                                            <i class="bi bi-x-square-fill" style="font-size: 1.5rem; color: red;"
                                                title="Kategoria NIE jest uwzględniana w rankingu."></i>
                                        @endif
                                        <div class="d-none">
                                            {{ $category->name_start }}
                                        </div>
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
