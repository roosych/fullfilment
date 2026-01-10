@extends('layouts.dashboard')

@section('title', 'Orders')

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
            Orders
        </li>
    </ul>
@endsection

@section('actions')
    <div class="d-flex align-items-center gap-3 gap-lg-5">
        <a href="{{route('dashboard.order.create')}}" class="btn btn-sm btn-flex btn-center btn-dark px-4">
            <i class="ki-duotone ki-plus-square fs-2 p-0 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            Создать заказ
        </a>
    </div>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="card card-flush">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <input type="text"
                           data-kt-ecommerce-order-filter="search"
                           class="form-control form-control-solid w-250px ps-12"
                           placeholder="Поиск заказа" />
                </div>
            </div>

            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <div class="input-group w-250px">
                    <input class="form-control form-control-solid rounded rounded-end-0"
                           placeholder="Выберите диапазон дат"
                           id="kt_ecommerce_sales_flatpickr" />
                    <button class="btn btn-icon btn-light"
                            id="kt_ecommerce_sales_flatpickr_clear">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </button>
                </div>

                <div class="w-100 mw-150px">
                    <!--begin::Select2-->
                    <select class="form-select form-select-solid"
                            data-control="select2"
                            data-hide-search="true"
                            data-placeholder="Статус"
                            data-kt-ecommerce-order-filter="status">
                        <option></option>
                        <option value="all">All</option>
                        @foreach(\App\Enums\OrderStatusEnum::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <!--end::Select2-->
                </div>

            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_sales_table">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="">ID</th>
                    <th class="min-w-175px">Мерчант</th>
                    <th class="text-center min-w-70px">Статус</th>
                    <th class="text-center min-w-100px">Товары / Кол-во</th>
                    <th class="text-center min-w-100px">Создан</th>
                    <th class="text-center min-w-100px">Статус доставки</th>
                    <th class="text-end min-w-100px">Действия</th>
                </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @foreach($orders as $order)
                        <tr>
                            <td data-kt-ecommerce-order-filter="order_id">
                                <a href="{{route('dashboard.order.show', $order)}}" class="text-gray-800 text-hover-primary fw-bold">
                                    #{{$order->id}}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <p class="text-gray-800 fs-5 fw-bold mb-0">
                                        {{$order->merchant->user->name}}
                                    </p>
                                    <span>
                                        {{$order->merchant->company}}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center pe-0" data-order="{{ $order->status->value }}">
                                <div class="badge badge-light-{{ $order->status->colorClass() }}">
                                    {{ $order->status->label() }}
                                </div>
                            </td>
                            <td class="text-center pe-0">
                                <span class="fw-bold text-gray-800">
                                    {{$order->items->count()}} / {{ $order->items->sum('quantity') }}
                                </span>
                            </td>
                            <td class="text-center" data-order="{{ $order->created_at->timestamp }}">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($order->created_at)->isoFormat('DD MMM Y, HH:mm') }}</span>
                            </td>
                            <td class="text-center">
                                @if($order->delivery)
                                    <div class="badge badge-light-{{ $order->delivery->status->colorClass() }}">
                                        {{ $order->delivery->status->label() }}
                                    </div>
                                @else
                                    Не создана
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{route('dashboard.order.show', $order)}}" class="btn btn-icon btn-sm btn-light w-25px h-25px">
                                    <i class="ki-duotone ki-black-right fs-2 text-muted">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('vendor_css')

@endpush

@push('vendor_js')
    <script src="{{asset('assets/js/plugins/datatables.bundle.js')}}"></script>
@endpush

@push('custom_js')
    <script src="{{asset('assets/js/custom/orders/listing.js')}}"></script>
@endpush
