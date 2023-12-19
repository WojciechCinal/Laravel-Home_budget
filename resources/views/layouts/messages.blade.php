@if (session('message'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-info-circle-fill" style="font-size: 1rem"></i> {{ session('message') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> {{ session('success') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill" style="font-size: 1rem"></i> {{ session('error') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
