@extends('layouts.app')

@section('content')
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edytuj nazwę kategorii</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="newCategoryName" class="form-control" placeholder="Aktualna nazwa kategorii">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryChanges">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-category').on('click', function() {
                let categoryId = $(this).data('id');
                let currentCategoryName = $(this).closest('tr').find('td:first').text();

                // Wypełnienie pola tekstowego aktualną nazwą kategorii
                $('#newCategoryName').attr('placeholder', currentCategoryName);

                // Wyświetlenie okna modalnego do edycji nazwy kategorii
                $('#editCategoryModal').modal('show');

                $('#saveCategoryChanges').on('click', function() {
                    let newCategoryName = $('#newCategoryName').val();

                    if (newCategoryName !== null && newCategoryName !== '') {
                        // Pobranie tokena CSRF z meta tagu
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        $.ajax({
                            url: '/categoryUpdate/' + categoryId,
                            type: 'POST',
                            data: {
                                _method: 'PUT',
                                name_category: newCategoryName
                            },
                            success: function(response) {
                                // Ukrycie okna modalnego po zakończeniu edycji
                                $('#editCategoryModal').modal('hide');
                                location.reload();
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#newCategoryName').keypress(function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $('#saveCategoryChanges').click();
                }
            });
        });
    </script>


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
                                    <th>Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name_category }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm edit-category"
                                                data-id="{{ $category->id_category }}">Edytuj</button>
                                        </td>
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
