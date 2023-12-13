@extends('layouts.app')

@section('content')
    <div class="modal fade" id="editSubCategoryModal" tabindex="-1" aria-labelledby="editSubCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubCategoryModalLabel">Edytuj nazwę podkategorii</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="newSubCategoryName" class="form-control"
                        placeholder="Aktualna nazwa podkategorii">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" id="saveSubCategoryChanges">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('.edit-subcategory').on('click', function() {
                let subCategoryId = $(this).data('id');
                let subCategoryName = $(this).closest('tr').find('td:first').text();

                // Ustawienie wartości pola tekstowego na aktualną nazwę podkategorii
                $('#newSubCategoryName').val(subCategoryName);

                $('#editSubCategoryModal').modal('show');

                $('#saveSubCategoryChanges').off('click').on('click', function() {
                    let newName = $('#newSubCategoryName').val();

                    if (newName !== null && newName !== '') {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        $.ajax({
                            url: `/subcategory/${subCategoryId}/updateName`,
                            type: 'POST',
                            data: {
                                _method: 'PUT',
                                name_subCategory: newName
                            },
                            success: function(response) {
                                $('#editSubCategoryModal').modal('hide');

                                // Aktualizacja nazwy wiersza w tabeli
                                $(`.edit-subcategory[data-id="${subCategoryId}"]`)
                                    .closest('tr').find('.subcategory-name').text(
                                        newName);

                                $(`.edit-subcategory[data-id="${subCategoryId}"]`)
                                    .closest(
                                        'tr').find('td:first').text(newName);

                                $('#messages').html(
                                    '<div class="alert alert-success" role="alert">Nazwa podkategorii zaktualizowana pomyślnie!</div>'
                                );
                            },

                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });

                $('#newSubCategoryName').off('keypress').on('keypress', function(e) {
                    if (e.which == 13) {
                        $('#saveSubCategoryChanges').click();
                        return false;
                    }
                });
            });

            $('.subcategory-status').on('change', function() {
                let subCategoryId = $(this).data('id');
                let isActive = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    url: `/subcategory/${subCategoryId}/updateStatus`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        is_active: isActive
                    },
                    success: function(response) {
                        // Aktualizacja stanu checkboxa na podstawie odpowiedzi AJAX
                        $(`.subcategory-status[data-id="${response.id}"]`).prop('checked',
                            response.isActive);
                        $('#messages').html(
                            '<div class="alert alert-success" role="alert">Pomyślnie zmieniono status.</div>'
                        );
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="messages"></div>
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <span class="align-middle"> Kategoria: {{ $category->name_category }}</span>
                        <div class="ms-auto">
                            <a href="{{ route('subCategory.new', ['id' => $category->id_category]) }}"
                                class="btn btn-success btn-sm mx-2">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                                podkategoria
                            </a>
                            <a href="{{ route('category.list') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-escape align-middle" style="font-size: 1rem;"></i>
                                Powrót
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('layouts.messages')

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-4 col-sm-4">Nazwa podkategorii</th>
                                    <th class="col-4 col-sm-4 text-center">Opcje</th>
                                    <th class="col-4 col-sm-4 text-center">Czy aktywna?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subCategories as $subCategory)
                                    <tr>
                                        <td class="col-4 col-sm-4 align-middle">{{ $subCategory->name_subCategory }}</td>
                                        <td class="col-4 col-sm-4 text-center">
                                            <button class="btn btn-warning btn-sm edit-subcategory"
                                                data-id="{{ $subCategory->id_subCategory }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>
                                        </td>
                                        <td class="col-4 col-sm-4 text-center">
                                            <input type="checkbox" class="form-check-input subcategory-status"
                                                data-id="{{ $subCategory->id_subCategory }}"
                                                {{ $subCategory->is_active ? 'checked' : '' }}>
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
