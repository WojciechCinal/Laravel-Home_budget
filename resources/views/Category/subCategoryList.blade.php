@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages"></div>
                <div class="card">

                    <div class="card-header">{{ __('Moje kategorie') }}</div>

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
