<div class="modal fade" id="deleteTransactionModal" tabindex="-1" role="dialog"
    aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Czy na pewno chcesz usunąć tę transakcję?</h5>
            </div>
            <div class="modal-footer">
                <button id="cancelTransactionButton" type="button" class="btn btn-secondary"
                    data-dismiss="modal">Anuluj</button>
                <button id="confirmTransactionButton" type="button" class="btn btn-danger">Usuń</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
        popovers.forEach(function(popover) {
            new bootstrap.Popover(popover, {
                placement: popover.dataset.bsPlacement,
                title: popover.dataset.bsTitle ? popover.dataset.bsTitle : '',
                content: function() {
                    return popover.dataset.bsContent;
                },
            });
        });
    });

    $(document).ready(function() {
        $('.deleteButton').on('click', function() {
            var id = $(this).data('transaction-id');

            $('#deleteTransactionModal').modal('show');
            currentId = id;

            var transactionName = $(this).closest('tr').find('td:first-child').text();
            $('#deleteTransactionModal .modal-body h5').text(
                `Czy na pewno chcesz usunąć transakcję "${transactionName}"?`);
        });

        $('#confirmTransactionButton').on('click', function() {
            $.ajax({
                type: 'POST',
                url: '/transactions/' + currentId,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteTransactionModal').modal('hide');
                    $(`.deleteButton[data-transaction-id="${currentId}"]`).closest('tr')
                        .remove();
                    $('#messages').html(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Transakcja została usunięta.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`);
                },
                error: function(response) {
                    $('#deleteTransactionModal').modal('hide');
                    $('#messages').html(`
                    <div class="alert alert-danger" role="alert">
                        <strong>Błąd!</strong> Wystąpił problem podczas usuwania transakcji.
                    </div>`);
                }
            });
        });

        $('#cancelTransactionButton').on('click', function() {
            $('#deleteTransactionModal').modal('hide');
            currentId = null;
        });
    });
</script>
