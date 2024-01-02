<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5> Czy na pewno chcesz usunąć listę <b><span id="listName"></span></b> ?</h5>
            </div>
            <div class="modal-footer">
                <button id="cancelButton" type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                <button id="confirmButton" type="button" class="btn btn-danger">Usuń</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.deleteButton').on('click', function() {
            var id = $(this).data('list-id');
            var listName = $(this).closest('.card').find('.card-header h5').text();

            $('#listName').text(listName);
            $('#deleteModal').modal('show');
            currentId = id;

            $('#confirmButton').data('list-title', listName);
        });

        $('#confirmButton').on('click', function() {
            var listName = $(this).data('list-title');

            $.ajax({
                type: 'POST',
                url: '/savingsPlans/' + currentId,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');

                    $(`.deleteButton[data-list-id="${currentId}"]`).closest('#SPlan')
                        .remove();

                    $('#messages').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert"> <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Plan oszczędnościowy: ' +
                        listName +
                        ' została usunięty. </strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                    );
                },
                error: function(response) {
                    $('#deleteModal').modal('hide');
                    $('#messages').html(
                        '<div class="alert alert-danger" role="alert"><strong>Błąd!</strong> Wystąpił problem podczas usuwania.</div>'
                    );
                }
            });
        });

        $('#cancelButton').on('click', function() {
            $('#deleteModal').modal('hide');
            currentId = null;
        });

    });
</script>
