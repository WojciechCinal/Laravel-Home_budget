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

@foreach ($savingsPlans as $savingsPlan)
    <div class="modal fade" id="savingsPlanDetails{{ $savingsPlan->id_savings_plan }}" tabindex="-1"
        aria-labelledby="savingsPlanDetails{{ $savingsPlan->id_savings_plan }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-bg-secondary text-light">
                    <h5 class="modal-title overflow-ellipsis"
                        id="savingsPlanDetails{{ $savingsPlan->id_savings_plan }}Label">
                        <b> {{ $savingsPlan->name_savings_plan }}</b>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>Priorytet:</th>
                            <td style="text-align: right;">
                                {{ $savingsPlan->priority->name_priority }}
                            </td>
                        </tr>
                        <tr class="table-secondary">
                            <th>Planowana data zakończenia:</th>
                            <td style="text-align: right;">
                                {{ $savingsPlan->formatted_end_date_savings_plan }}</td>
                        </tr>
                        <tr>
                            <th>Kwota / cel:</th>
                            <td style="text-align: right;">{{ $savingsPlan->amount_savings_plan }} /
                                {{ $savingsPlan->goal_savings_plan }} PLN </td>
                        </tr>
                        <tr class="table-secondary">
                            <th>Data rozpoczęcia:</th>
                            <td style="text-align: right;"> {{ $savingsPlan->formatted_created_at }}
                            </td>
                        </tr>
                        @if ($savingsPlan->months_remaining == 0)
                            <tr class="table-danger">
                                <th>Pozostało:</th>
                                <td style="text-align: right; color: red; font-weight: bold;">
                                    {{ $savingsPlan->deadline }}
                                </td>
                            </tr>
                            <tr class="table-secondary">
                                <th>Proponowana wpłata miesięczna:</th>
                                <td style="text-align: right; color: red; font-weight: bold;">
                                    {{ $savingsPlan->monthly_deposit_needed }} PLN
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th>Pozostało:</th>
                                <td style="text-align: right;"> {{ $savingsPlan->months_remaining }}
                                </td>
                            </tr>
                            <tr class="table-secondary">
                                <th>Proponowana wpłata miesięczna:</th>
                                <td style="text-align: right;"> {{ $savingsPlan->monthly_deposit_needed }} PLN
                                </td>
                            </tr>
                        @endif
                        <tr class="table-info">
                            <th>Brakująca kwota:</th>
                            <td style="text-align: right;">
                                {{ $savingsPlan->goal_savings_plan - $savingsPlan->amount_savings_plan}} PLN </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    @if($savingsPlan->is_completed == 0)
                    <form method="POST"
                        action="{{ route('savings-plans.update-amount', ['id' => $savingsPlan->id_savings_plan]) }}">
                        @csrf
                        <div class="input-group">
                            <input type="number" class="form-control" id="increaseAmount" name="increase_amount"
                                required>
                            <button type="submit" class="btn btn-primary ms-2">Wpłać</button>
                        </div>
                    </form>
                    @endif
                    <div>
                        @if($savingsPlan->is_completed == 0)
                        <a href="{{ route('savings-plans.edit', ['id' => $savingsPlan->id_savings_plan]) }}"
                            class="btn btn-primary me-2">
                            <i class="bi bi-pencil-square align-middle" style="font-size: 1rem;"></i>
                            Edytuj
                        </a>
                        @endif()
                        <button class="btn btn-danger deleteButton" data-list-id="{{ $savingsPlan->id_savings_plan }}"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash3-fill align-middle" style="font-size: 1rem;"></i>
                            Usuń
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endforeach

<script>
    $(document).ready(function() {
        $('.deleteButton').on('click', function() {
            var id = $(this).data('list-id');
            var modalId = $(this).closest('.modal').attr('id'); // Pobieramy ID aktualnego modala
            var listName = $('#' + modalId + ' .modal-title').text(); // Pobieramy tekst z tytułu modala
            $('#listName').text(listName);
            $('#deleteModal').modal('show');
            currentId = id;
            $('#confirmButton').data('list-title',
                listName); // Aktualizacja nazwy dla potwierdzenia usunięcia
        });

        $('#confirmButton').on('click', function() {
            var listName = $(this).data('list-title');

            $.ajax({
                type: 'POST',
                url: '/savingsPlans/delete/' + currentId,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');

                    $(`#SPlan-${currentId}`).remove();

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

        $('.detailsButton').on('click', function() {
            $('#detailsModal').modal('show');
        });

    });
</script>
