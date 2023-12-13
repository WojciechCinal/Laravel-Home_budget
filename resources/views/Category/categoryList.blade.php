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
            // Obsługa zdarzenia kliknięcia "Zapisz zmiany" poza zdarzeniem dla przycisku "Edytuj"
            $('#saveCategoryChanges').on('click', function() {
                // Pobranie danych z modala
                let categoryId = $('#editCategoryModal').data('category-id');
                let newCategoryName = $('#newCategoryName').val();

                if (newCategoryName !== null && newCategoryName !== '') {
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
                            $('#editCategoryModal').modal('hide');
                            $('#editCategoryModal').on('hidden.bs.modal', function(e) {
                                $('#newCategoryName').val('');
                                $('#newCategoryName').attr('placeholder', '');
                            });

                            $(`.edit-category[data-id="${categoryId}"]`).closest('tr').find(
                                'td:first').text(newCategoryName);

                            $('#messages').html(
                                '<div class="alert alert-success" role="alert">Nazwa kategorii zaktualizowana pomyślnie!</div>'
                                );
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            $('#messages').html(
                                '<div class="alert alert-danger" role="alert">Wystąpił błąd przy edycji nazwy</div>'
                                );
                        }
                    });
                }
            });

            $('.edit-category').on('click', function() {
                let categoryId = $(this).data('id');
                let currentCategoryName = $(this).closest('tr').find('td:first').text();

                $('#newCategoryName').val(currentCategoryName);
                $('#editCategoryModal').data('category-id',
                categoryId); // Przypisanie ID kategorii do modala

                $('#editCategoryModal').modal('show');
            });

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
                <div id="messages"></div>
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <span class="align-middle">{{ __('Moje kategorie') }}</span>
                        <div class="ms-auto">
                            <a href="{{ route('create.category') }}" class="btn btn-success btn-sm mx-2">
                                <i class="bi bi-bookmark-plus-fill align-middle" style="font-size: 1rem;"></i> Nowa
                                kategoria
                            </a>
                            <a href="{{ route('category.archiveList') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-archive-fill align-middle" style="font-size: 1rem;"></i> Archiwum
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('layouts.messages')

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-6 col-sm-4">Nazwa kategorii</th>
                                    <th class="col-6 col-sm-4 text-center">Opcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="col-6 col-sm-4 align-middle">{{ $category->name_category }}</td>
                                        <td class="col-6 col-sm-4 text-center">
                                            <button class="btn btn-warning btn-sm edit-category"
                                                data-id="{{ $category->id_category }}">
                                                <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                                                Edytuj
                                            </button>

                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('subCategory.list', ['id' => $category->id_category]) }}">
                                                <i class="bi bi-list-task  align-middle" style="font-size: 1rem;"></i> Pokaż
                                                podkategorie</a>

                                            <a href="{{ route('archive.category', ['id' => $category->id_category]) }}"
                                                class="btn btn-secondary btn-sm">
                                                <i class="bi bi-archive align-middle" style="font-size: 1rem;"></i>
                                                Archiwizuj
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
