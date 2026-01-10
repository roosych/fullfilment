@php
    $classes = match($type) {
        'success' => 'success',
        'error' => 'danger',
        'warning' => 'warning',
        'info' => 'info',
        default => 'danger',
    };
@endphp

@if($message)
    <div class="alert alert-{{ $classes }} d-flex align-items-center p-5" role="alert">
        <i class="ki-duotone ki-shield-tick fs-2hx text-{{ $classes }} me-4">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <div class="d-flex flex-column">
            <span class="fs-5">{!! $message !!}</span>
        </div>
    </div>
@endif
