@extends('layouts.app')

@section('content')
    {{-- @include('shopping_lists.modal') --}}
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Moje transakcje.</h1>
            <a href="{{ route('transactions.create') }}" class="btn btn-success btn-sm mx-2">
                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa transakcja
            </a>
        </div>
        <div id="messages">@include('layouts.messages')</div>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Kwota</th>
                        <th>Data</th>
                        <th>Kategoria</th>
                        <th>Podkategoria</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->name_transaction }}</td>
                            <td>{{ $transaction->amount_transaction }}</td>
                            <td>{{ $transaction->date_transaction }}</td>
                            <td>{{ $transaction->category->name_category }}</td>
                            <td>
                                @if ($transaction->id_subCategory == null)
                                    -
                                @else
                                    {{ $transaction->subcategory->name_subCategory }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="nav justify-content-center mt-2">
            {{-- {{ $shoppingLists->links() }} --}}
        </div>
    </div>
@endsection
