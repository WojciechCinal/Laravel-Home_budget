<div class="modal fade modal-md" id="generateRankingModal" tabindex="-1" role="dialog"
    aria-labelledby="generateRankingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center;">
                <p class="h3">Wybierz rodzaj rankingu.</p>
                <hr>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-dark btn-lg col" data-bs-toggle="modal"
                        data-bs-target="#yearlyRankingModal"><b>ROCZNY</b></button>
                    <button type="button" class="btn btn-outline-dark btn-lg col" data-bs-toggle="modal"
                        data-bs-target="#monthlyRankingModal"><b>MIESIĘCZNY</b></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla rankingu rocznego -->
<div class="modal fade" id="yearlyRankingModal" tabindex="-1" aria-labelledby="yearlyRankingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center;" class="h3"><b>Ranking: ROCZNY</b></p>
                <hr>
                <form action="{{ route('ranking.index') }}" method="GET">
                    <input type="hidden" name="ranking_type" value="yearly">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="date" class="form-label"><b>Wybierz rok</b></label>
                            <input type="number" class="form-control" id="date" name="date" min="2019"
                                max="2100" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info mt-3"><b>WYŚWIETL RANKING</b></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla rankingu miesięcznego -->
<div class="modal fade" id="monthlyRankingModal" tabindex="-1" aria-labelledby="monthlyRankingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center;" class="h3"><b>Ranking: MIESIĘCZNY</b></p>
                <hr>
                <form action="{{ route('ranking.index') }}" method="GET">
                    <input type="hidden" name="ranking_type" value="monthly">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="date" class="form-label"><b>Wybierz miesiąc:</b></label>
                            <input type="month" class="form-control" id="date" name="date" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info mt-3"><b>WYŚWIETL RANKING</b></button>
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
        var myModal = new bootstrap.Modal(document.getElementById('generateRankingModal'), {
            keyboard: false
        });

        $('#generateRankingModal').on('show.bs.modal', function() {
            myModal.hide();
        });
    });
</script>
