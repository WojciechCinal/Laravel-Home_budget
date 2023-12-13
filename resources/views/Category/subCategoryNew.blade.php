@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Dodaj nową podkategorię do: {{ $category->name_category }}
                        </div>
                        <div>
                            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('subCategory.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id_category }}">

                            <div class="mb-3">
                                <label for="name_subCategory" class="form-label">Nazwa podkategorii</label>
                                <input type="text" class="form-control" id="name_subCategory" name="name_subCategory">
                            </div>

                            <button type="submit" class="btn btn-primary">Dodaj</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
