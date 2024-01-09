@if (is_array(session('message')))
    @foreach (session('message') as $msg)
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-info-circle-fill" style="font-size: 1rem"></i> {{ $msg }} </strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
    @php
        session()->forget('message');
    @endphp
@elseif (is_string(session('message')))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-info-circle-fill" style="font-size: 1rem"></i> {{ session('message') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php
        session()->forget('message');
    @endphp
@endif

@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> {{ session('success') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session()->has('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-diamond-fill" style="font-size: 1rem;"></i> {{ session('warning') }}
        </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill" style="font-size: 1rem"></i> {{ session('error') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session()->has('sortSavingsPlans'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-info-circle-fill" style="font-size: 1rem"></i>
            {{ session('sortSavingsPlans') }}
        </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php
        session()->forget('sortSavingsPlans');
    @endphp
@endif

@if (session()->has('yearReportMessages'))
    @foreach (session('yearReportMessages') as $message)
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-info-circle-fill" style="font-size: 1rem"></i> {{ $message }} </strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
    @php
        session()->forget('yearReportMessages');
    @endphp
@endif
