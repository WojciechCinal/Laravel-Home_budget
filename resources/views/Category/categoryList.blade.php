@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Your Categories') }}</div>

                    <div class="card-body">
                        <h4>Categories:</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name_category }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">{{ __('Your Subcategories') }}</div>

                    <div class="card-body">
                        <h4>Subcategories:</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subcategories as $subcategory)
                                    <tr>
                                        <td>{{ $subcategory->name_subCategory }}</td>
                                        <td>{{ $subcategory->category->name_category }}</td>
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
