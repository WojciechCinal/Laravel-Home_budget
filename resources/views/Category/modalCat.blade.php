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
