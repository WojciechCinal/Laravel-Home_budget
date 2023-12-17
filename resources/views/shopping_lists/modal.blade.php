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
