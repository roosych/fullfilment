@extends('layouts.dashboard')

@section('title', 'Заказ #' . $order->id)

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('cabinet.dashboard')}}" class="text-muted text-hover-primary">
                Главная
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{route('cabinet.orders.index')}}" class="text-muted text-hover-primary">
                Доставки
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Заказ #{{ $order->id }}
        </li>
    </ul>
@endsection

@section('content')
    <div class="d-flex flex-column gap-7 gap-lg-10">
        <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-lg-n2 me-auto" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4 active"
                       data-bs-toggle="tab"
                       href="#kt_order_summary"
                       aria-selected="true" role="tab">
                        Информация о заказе
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade active show" id="kt_order_summary" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                        <!-- Информация о доставке -->
                        <div class="card card-flush py-4 flex-row-fluid position-relative">
                            <div class="position-absolute top-0 end-0 bottom-0 opacity-10 d-flex align-items-center me-5">
                                <i class="ki-duotone ki-delivery" style="font-size: 13em">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>

                            <div class="card-header">
                                <div class="card-title"><h2>Информация о доставке</h2></div>
                            </div>

                            <div class="card-body pt-0">
                                <div class="mb-3">
                                    <span class="text-gray-600">Получатель:</span>
                                    <span class="fs-5 fw-bold text-gray-800 ms-2">
                                        {{$order->recipient_name}}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-gray-600">Телефон:</span>
                                    <span class="fs-5 fw-bold text-gray-800 ms-2">
                                        {{$order->recipient_phone}}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-gray-600">Адрес:</span>
                                    <span class="fs-5 fw-bold text-gray-800 ms-2">
                                        {{$order->recipient_address}}
                                    </span>
                                </div>
                                @if($order->notes)
                                    <div class="mb-3">
                                        <span class="text-gray-600">Примечания:</span>
                                        <span class="fs-5 fw-bold text-gray-800 ms-2">
                                            {{$order->notes}}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($order->zone)
                                    <div class="mb-3">
                                        <span class="text-gray-600">Зона доставки:</span>
                                        <span class="fs-5 fw-bold text-gray-800 ms-2">
                                            {{$order->zone->name}}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($order->rate)
                                    <div class="mb-3">
                                        <span class="text-gray-600">Тариф:</span>
                                        <span class="fs-5 fw-bold text-gray-800 ms-2">
                                            {{$order->rate->name}}
                                        </span>
                                    </div>
                                @endif

                                @if($order->delivery)
                                    <div class="mb-3">
                                        <span class="text-gray-600">Статус доставки:</span>
                                        <span class="badge badge-light-{{ $order->delivery->status->colorClass() }} ms-2">
                                            {{ $order->delivery->status->label() }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="text-gray-600">Стоимость доставки:</span>
                                        <span class="fs-5 fw-bold text-gray-800 ms-2">
                                            {{ money($order->delivery->price) }}
                                        </span>
                                    </div>
                                    @if($order->delivery->courier)
                                        <div class="mb-3">
                                            <span class="text-gray-600">Курьер:</span>
                                            <span class="fs-5 fw-bold text-gray-800 ms-2">
                                                {{$order->delivery->courier->name}} ({{$order->delivery->courier->phone}})
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Товары в заказе -->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Заказ #{{$order->id}}</h2>
                                <div class="ms-3 fs-6 badge badge-light-{{$order->status->colorClass()}}">
                                    {{$order->status->label()}}
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                    <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-175px">Товар</th>
                                        <th class="min-w-100px text-end">SKU</th>
                                        <th class="w-150px text-end">Количество</th>
                                    </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <p class="fw-bold text-gray-800 mb-0">
                                                            {{ $item->product->name }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $item->product->sku }}</td>
                                            <td class="text-end">{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($order->delivery)
                                        <tr>
                                            <td colspan="2" class="fs-3 text-gray-800 text-end">Стоимость доставки</td>
                                            <td class="text-gray-800 fs-3 fw-bolder text-end">
                                                {{ money($order->delivery->price) }}
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

