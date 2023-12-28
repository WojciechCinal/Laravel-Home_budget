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

<div class="modal fade" id="fullScreenModal" tabindex="-1" role="dialog" aria-labelledby="fullScreenModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content bg-warning">
            <div class="modal-header">
                <h3><b><span id="fullScreenListName"></span></b></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="white-space: pre-line; font-size:18px;" id="fullScreenListDescription"></p>
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
                listName);
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
                        '<div class="alert alert-success alert-dismissible fade show" role="alert"> <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Lista ' +
                        listName +
                        ' została usunięta. </strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                    );

                },
                error: function(response) {
                    '<div class = "alert alert-danger alert-dismissible fade show"role = "alert" ><strong > < i class = "bi bi-exclamation-triangle-fill"style = "font-size: 1rem" > </i> Wystąpił błąd podczas usuwania listy! </strong ><button type = "button" class = "btn-close" data - bs - dismiss = "alert" aria - label = "Close" > < /button> </div>'
                }
            });
        });

        $('#cancelButton').on('click', function() {
            $('#deleteModal').modal('hide');
            currentId = null;
        });

        $('.zoomButton').on('click', function() {
            var id = $(this).data('list-id');
            var listName = $(this).closest('.card').find('.card-header h5').text();
            var hiddenDescription = $(this).closest('.card').find('.d-none p').text();

            $('#fullScreenListName').text(listName);
            $('#fullScreenListDescription').text(hiddenDescription);

            $('#fullScreenModal').modal('show');
        });

    });
</script>
