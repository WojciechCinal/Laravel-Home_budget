@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="mt-3">Archiwum kategorii</h4>
                        <div class="ms-auto">
                            <a href="{{ route('category.list') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1.3rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-6 col-sm-4">Nazwa kategorii</th>
                                    <th class="col-6 col-sm-4 text-center">Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($archivedCategories as $category)
                                    <tr>
                                        <td class="col-6 col-sm-4 align-middle">{{ $category->name_category }}</td>
                                        <td class="col-6 col-sm-4 text-center">
                                            <a href="{{ route('category.restore', ['id' => $category->id_category]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-box-arrow-up align-middle" style="font-size: 1rem;"></i>
                                                Przywróć
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
