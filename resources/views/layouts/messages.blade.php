@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

@if (session('message'))
    <div class="alert alert-info" role="alert">
        {{ session('message') }}
    </div>
@endif

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> {{ session('success') }} </strong>
     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
 </div>
@endif

@if (session('error'))
    <div class="alert alert-error alert-dismissible fade show" role="alert">
       <strong> {{ session('error') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
