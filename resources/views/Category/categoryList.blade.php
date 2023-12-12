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
                                $('#editCategoryModal').on('hidden.bs.modal', function(
                                    e) {
                                    $('#newCategoryName').val('');
                                    $('#newCategoryName').attr('placeholder',
                                        '');
                                });

                                // Aktualizacja nazwy na stronie bez odświeżania
                                $(`.edit-category[data-id="${categoryId}"]`).closest(
                                    'tr').find('td:first').text(newCategoryName);
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
                    <div class="card-header">{{ __('Moje kategorie') }}</div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nazwa kategorii</th>
                                    <th>Nazwa początkowa</th>
                                    <th>Opcje</th>
                                    <th>Podkategorie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name_category }}</td>
                                        <td>{{ $category->name_start }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm edit-category"
                                                data-id="{{ $category->id_category }}">Edytuj</button>

                                        <td>
                                            @foreach ($subcategories as $subcategory)
                                                @if ($category->id_category === $subcategory->id_category)
                                                    <li><a>{{ $subcategory->name_subCategory }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </td>

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
