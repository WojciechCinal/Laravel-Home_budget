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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="nameContent"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="nameStartContent">
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#saveCategoryChanges').on('click', function() {
            let categoryId = $('#editCategoryModal').data('category-id');
            let newCategoryName = $('#newCategoryName').val().trim();

            // Walidacja - sprawdzenie długości nazwy kategorii
            if (newCategoryName.length < 3 || newCategoryName.length > 60) {
                alert('Nazwa kategorii powinna mieć od 3 do 60 znaków.');
                return;
            }

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
                            '<div class="alert alert-success alert-dismissible fade show" role="alert"> <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Nazwa kategorii zaktualizowana pomyślnie. </strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        $('#messages').html(
                            '<div class = "alert alert-danger alert-dismissible fade show"role = "alert" ><strong > < i class = "bi bi-exclamation-triangle-fill"style = "font-size: 1rem" > </i> Wystąpił błąd przy zmianie nazwy! </strong ><button type = "button" class = "btn-close" data - bs - dismiss = "alert" aria - label = "Close" > < /button> </div>'
                        );
                    }
                });
            }
        });
        $('.edit-category').on('click', function() {
            let categoryId = $(this).data('id');
            let currentCategoryName = $(this).closest('tr').find('td:first').text().trim();

            $('#newCategoryName').val(currentCategoryName);
            $('#editCategoryModal').data('category-id',
                categoryId);

            $('#editCategoryModal').modal('show');
        });

        $('#newCategoryName').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $('#saveCategoryChanges').click();
            }
        });

        let toastInstance = null;

        function showCategoryToast(categoryStartName, categoryName) {
            const nameStartContent = `${categoryName}`;
            $('#nameContent').text(nameStartContent);

            const nameContent = `Uzwględniona w rankingu jako: ${categoryStartName}`;
            $('#nameStartContent').text(nameContent);

            if (!toastInstance) {
                var toastLiveExample = document.getElementById('liveToast');
                toastInstance = new bootstrap.Toast(toastLiveExample, {
                    delay: 4000
                });
            }

            toastInstance.show();
        }

        $('.start-name').on('click', function() {
            const categoryStartName = $(this).closest('tr').find('td:last').text().trim();
            const categoryName = $(this).closest('tr').find('td:first').text().trim();

            showCategoryToast(categoryStartName, categoryName);
        });


    });
</script>
