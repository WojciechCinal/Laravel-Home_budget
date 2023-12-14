@extends('layouts.app')

@section('content')
    <!-- Modal usuwania -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Potwierdzenie usunięcia</h5>
                </div>
                <div class="modal-body">
                    Czy na pewno chcesz usunąć listę <span id="listName"></span>?
                </div>
                <div class="modal-footer">
                    <div class="modal-footer">
                        <button id="cancelButton" type="button" class="btn btn-secondary"
                            data-dismiss="modal">Anuluj</button>
                        <button id="confirmButton" type="button" class="btn btn-danger">Usuń</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var currentId;

            $('.deleteButton').on('click', function() {
                var id = $(this).data('list-id');
                var listName = $(this).closest('.card').find('.card-header h5').text();

                $('#listName').text(listName);
                $('#deleteModal').modal('show');
                currentId = id;

                $('#confirmButton').data('list-title',
                    listName); // Przekazanie nazwy listy do przycisku potwierdzającego
            });

            $('#confirmButton').on('click', function() {
                var listName = $(this).data('list-title');
                $.ajax({
                    type: 'POST',
                    url: '/shoppingLists/' + currentId,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');

                        $(`.deleteButton[data-list-id="${currentId}"]`).closest('.col-sm-4')
                            .remove();

                        $('#messages').html(
                            '<div class="alert alert-success" role="alert">Lista ' +
                            listName + ' została usunięta.</div>'
                        );

                    },
                    error: function(error) {
                        console.error('Wystąpił błąd podczas usuwania listy:', error);
                    }
                });
            });


            $('#cancelButton').on('click', function() {
                $('#deleteModal').modal('hide');
                currentId = null;
            });
        });
    </script>
    <div class="container">
        <h1>Twoje listy zakupów</h1>
        @include('layouts.messages')
        <div id="messages"></div>
        <div class="row">
            @foreach ($shoppingLists as $list)
                <div class="col-sm-4 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $list->title_shopping_list }}</h5>
                            <small class="text-muted">{{ $list->formatted_updated_at }}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $list->description_shopping_list }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-primary mb-0">Edytuj</a>
                                <button class="btn btn-danger deleteButton"
                                    data-list-id="{{ $list->id_shopping_list }}">Usuń</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
