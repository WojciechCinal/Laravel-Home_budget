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

            $('#newSubCategoryName').val(subCategoryName);

            $('#editSubCategoryModal').modal('show');

            $('#saveSubCategoryChanges').off('click').on('click', function() {
                let newName = $('#newSubCategoryName').val().trim();

                if (newName.length < 3 || newName.length > 60) {
                alert('Nazwa podkategorii powinna mieć od 3 do 60 znaków.');
                return;
            }

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

                            $(`.edit-subcategory[data-id="${subCategoryId}"]`)
                                .closest('tr').find('.subcategory-name').text(
                                    newName);

                            $(`.edit-subcategory[data-id="${subCategoryId}"]`)
                                .closest(
                                    'tr').find('td:first').text(newName);

                            $('#messages').html(
                                '<div class="alert alert-success alert-dismissible fade show" role="alert"> <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Nazwa podkategorii zaktualizowana pomyślnie. </strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                            );
                        },

                        error: function(response) {
                            '<div class = "alert alert-danger alert-dismissible fade show"role = "alert" ><strong > < i class = "bi bi-exclamation-triangle-fill"style = "font-size: 1rem" > </i> Wystąpił błąd przy zmianie nazwy! </strong ><button type = "button" class = "btn-close" data - bs - dismiss = "alert" aria - label = "Close" > < /button> </div>'
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
                    $(`.subcategory-status[data-id="${response.id}"]`).prop('checked',
                        response.isActive);
                    $('#messages').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert"> <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Pomyślnie zmieniono status. </strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                    );
                },
                error: function(response) {
                    '<div class = "alert alert-danger alert-dismissible fade show"role = "alert" ><strong > < i class = "bi bi-exclamation-triangle-fill"style = "font-size: 1rem" > </i> Wystąpił błąd przy zmianie statusu! </strong ><button type = "button" class = "btn-close" data - bs - dismiss = "alert" aria - label = "Close" > < /button> </div>'
                }
            });
        });
    });
</script>
