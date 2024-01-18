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

<div class="modal fade modal-md" id="generateReportModal" tabindex="-1" role="dialog"
    aria-labelledby="generateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center;">
                <p class="h3">Wybierz rodzaj raportu.</p>
                <hr>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-dark btn-lg col" data-bs-toggle="modal"
                        data-bs-target="#yearlyReportModal"><b>ROCZNY</b></button>
                    <button type="button" class="btn btn-outline-dark btn-lg col" data-bs-toggle="modal"
                        data-bs-target="#monthlyReportModal"><b>MIESIĘCZNY</b></button>
                    <button type="button" class="btn btn-outline-dark btn-lg col" data-bs-toggle="modal"
                        data-bs-target="#weeklyReportModal"><b>TYGODNIOWY</b></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla raportu rocznego -->
<div class="modal fade" id="yearlyReportModal" tabindex="-1" aria-labelledby="yearlyReportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center;" class="h3"><b>Raport: ROCZNY</b></p>
                <hr>
                <form action="{{ route('generate.yearly.report') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_year" class="form-label"><b>Rok początkowy:</b></label>
                            <input type="number" class="form-control" id="start_year" name="start_year" min="2019"
                                max="2100"
                                value="{{ request()->input('start_date') ? \Carbon\Carbon::parse(request()->input('start_date'))->format('Y') : now()->year }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_year" class="form-label"><b>Rok końcowy:</b></label>
                            <input type="number" class="form-control" id="end_year" name="end_year" min="2019"
                                max="2100"
                                value="{{ request()->input('end_date') ? \Carbon\Carbon::parse(request()->input('end_date'))->format('Y') : now()->year }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-12 my-3">
                        <label><b>Kategorie:</b></label>
                        <div class="scrollable-checkboxes-year">
                            @foreach ($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                        value="{{ $category->id_category }}"
                                        {{ in_array($category->id_category, $selectedCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $category->name_category }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info"><b>GENERUJ RAPORT</b></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla raportu miesięcznego -->
<div class="modal fade" id="monthlyReportModal" tabindex="-1" aria-labelledby="monthlyReportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center;" class="h3"><b>Raport: MIESIĘCZNY</b></p>
                <hr>
                <form action="{{ route('generate.monthly.report') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="selected_year" class="form-label"><b>Wybierz rok:</b></label>
                            <input type="number" class="form-control" id="selected_year" name="selected_year"
                                min="2019" max="2100"
                                value="{{ request()->input('selected_year') ? request()->input('selected_year') : now()->year }}"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label for="selected_month_year_start" class="form-label"><b>Miesiąc
                                    początkowy:</b></label>
                            <select class="form-select" id="selected_month_year_start" name="start_date" required>
                                <option value="" disabled selected>Wybierz miesiąc</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}"
                                        {{ request()->input('start_date') == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $month, 1)->locale('pl')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="selected_month_year_end" class="form-label"><b>Miesiąc końcowy:</b></label>
                            <select class="form-select" id="selected_month_year_end" name="end_date" required>
                                <option value="" disabled selected>Wybierz miesiąc</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}"
                                        {{ request()->input('end_date') == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $month, 1)->locale('pl')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 my-3">
                        <label><b>Kategorie:</b></label>
                        <div class="scrollable-checkboxes-year">
                            @foreach ($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                        value="{{ $category->id_category }}"
                                        {{ in_array($category->id_category, $selectedCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $category->name_category }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info"><b>GENERUJ RAPORT</b></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla raportu tygodniowego -->
<div class="modal fade" id="weeklyReportModal" tabindex="-1" aria-labelledby="weeklyReportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center;" class="h3"><b>Raport: TYGODNIOWY</b></p>
                <hr>
                <form action="{{ route('generate.weekly.report') }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startWeek"><b>Tydzień początkowy:</b></label>
                            <input type="week" id="startWeek" name="startWeek" class="form-control"
                                value="{{ now()->format('Y-\WW') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endWeek"><b>Tydzień końcowy:</b></label>
                            <input type="week" id="endWeek" name="endWeek" class="form-control"
                                value="{{ now()->format('Y-\WW') }}" required>
                        </div>
                    </div>
                    <div class="col-md-12 my-3">
                        <label><b>Kategorie:</b></label>
                        <div class="scrollable-checkboxes-year">
                            @foreach ($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                        value="{{ $category->id_category }}"
                                        {{ in_array($category->id_category, $selectedCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $category->name_category }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info"><b>GENERUJ RAPORT</b></button>
                    </div>
                </form>
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

    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('generateReportModal'), {
            keyboard: false
        });

        $('#generateReportModal').on('show.bs.modal', function() {
            myModal.hide();
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
