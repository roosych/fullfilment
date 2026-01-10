@extends('layouts.dashboard')

@section('title', 'Merchants')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">
                Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Merchants
        </li>
    </ul>
@endsection

@section('actions')
    <div class="d-flex align-items-center gap-3 gap-lg-5">
        <a href="{{route('dashboard.merchants.create')}}" class="btn btn-sm btn-flex btn-center btn-dark px-4">
            <i class="ki-duotone ki-plus-square fs-2 p-0 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            Add merchant
        </a>
    </div>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="card">
        <div class="card-body py-4">
            <div class="d-flex align-items-center position-relative my-2 pt-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <input type="text" merchant-filter="search" class="form-control form-control-solid w-250px ps-12"
                       placeholder="Search..."/>
            </div>

            <div id="output" class="text-success fs-5 fw-bold output my-5"></div>

            <table class="table align-middle table-row-dashed fs-6 gy-5" id="merchants_table">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">{{__('Name')}}</th>
                    <th class="text-center">{{__('Company name')}}</th>
                    <th class="text-center">{{__('Products')}}</th>
                    <th class="text-center">{{__('Completed deliveries')}}</th>
                    <th class="text-center">{{__('Balance')}}</th>
                    <th class="text-end">{{__('Status')}}</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">

                @foreach($merchants as $merchant)
                    <tr>
                        <td class="d-flex align-items-center border-bottom-0">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                @if($merchant->avatar)
                                    <div class="symbol-label">
                                        <img src="{{$merchant->avatar}}" alt="{{$merchant->user->name}}"
                                             class="w-100"/>
                                    </div>
                                @else
                                    <div class="symbol-label fs-3 bg-light-dark text-dark">
                                        {{ mb_substr($merchant->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{route('dashboard.merchants.show', $merchant)}}" class="text-gray-800 text-hover-primary mb-1">
                                    {{$merchant->user->name}}
                                </a>
                                <span>{{$merchant->phone}}</span>
                            </div>
                        </td>
                        <td class="text-center pe-0">
                            <div class="d-flex flex-column">
                                @if($merchant->company)
                                    <a href="{{route('dashboard.merchants.show', $merchant)}}" class="text-gray-800 text-hover-primary mb-1">
                                        {{$merchant->company}}
                                    </a>
                                @else
                                    <em>без имени</em>
                                @endif
                                <span>{{$merchant->address}}</span>
                            </div>
                        </td>
                        <td class="text-center pe-0">
                            <a href="{{route('dashboard.merchants.stock', $merchant)}}" class="text-gray-800 text-hover-primary">
                                {{$merchant->products->count()}}
                            </a>
                        </td>
                        <td class="text-center pe-0">
                            df
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-inline-flex align-items-center justify-content-center">
                                <span id="merchant_balance_{{ $merchant->id }}" class="fs-5 fw-semibold text-gray-800">
                                    {{ money($merchant->balance) }}
                                </span>

                                <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-success toggle h-25px w-25px ms-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#top_up_modal"
                                        data-merchant-id="{{ $merchant->id }}">
                                    <i class="ki-duotone ki-plus fs-4 m-0 toggle-off">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <i class="ki-duotone ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-check-custom form-switch">
                                    <input class="form-check-input w-35px h-20px merchant-status-toggle"
                                           type="checkbox"
                                           id="activeSwitch{{$merchant->user->id}}"
                                           data-user-id="{{$merchant->user->id}}"
                                           data-url="{{ route('dashboard.merchants.toggle-status', $merchant) }}"
                                        {{$merchant->user->active ? 'checked' : ''}}>
                                    <label class="form-check-label" for="activeSwitch{{$merchant->user->id}}"></label>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                               data-kt-menu-trigger="click"
                               data-kt-menu-placement="bottom-end">
                                Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"><span class="path1"></span></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                 data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{route('dashboard.merchants.show', $merchant)}}" class="menu-link px-3">View</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="{{route('dashboard.merchants.stock', $merchant)}}" class="menu-link px-3">Stock</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.merchants.modals.top-up')
@endsection

@push('vendor_css')

@endpush

@push('vendor_js')
    <script src="{{asset('assets/js/plugins/datatables.bundle.js')}}"></script>
@endpush

@push('custom_js')
    <script src="{{asset('assets/js/custom/merchants/table.js')}}"></script>
    <script>
        $(document).ready(function() {
            // Обработка переключения статуса мерчанта
            $(document).on('change', '.merchant-status-toggle', function() {
                const $checkbox = $(this);
                const userId = $checkbox.data('user-id');
                const url = $checkbox.data('url');
                const isChecked = $checkbox.is(':checked');

                // Сохраняем текущее состояние для возможного отката
                const previousState = !isChecked;

                // Блокируем чекбокс во время запроса
                $checkbox.prop('disabled', true);

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Обновляем состояние чекбокса
                            $checkbox.prop('checked', response.active);
                            
                            // Показываем уведомление об успехе, если Swal доступен
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        // Откатываем состояние чекбокса при ошибке
                        $checkbox.prop('checked', previousState);
                        
                        const message = xhr.responseJSON?.message || 'An error occurred while updating the status';
                        
                        // Показываем уведомление об ошибке, если Swal доступен
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message,
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            alert(message);
                        }
                    },
                    complete: function() {
                        // Разблокируем чекбокс после завершения запроса
                        $checkbox.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
