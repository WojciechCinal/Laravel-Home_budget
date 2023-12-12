@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages"></div>
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <span class="align-middle">{{ __('Moje podkategorie') }}</span>
                        <div class="ms-auto">
                            <a href="{{ route('category.list') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @foreach ($subCategories as $subCategory)
                            <li><a>{{ $subCategory->name_subCategory }}</a>
                            </li>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
