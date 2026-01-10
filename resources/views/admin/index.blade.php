@extends('layouts.dashboard')

@section('title', 'Панель управления')

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <!-- Статистические карточки -->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!-- Заказы -->
        <div class="col-xl-3">
            <div class="card card-flush mb-5 mb-xl-10">
                <div class="card-header pt-5 pb-3">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ $totalOrders }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Всего заказов</span>
                    </div>
                </div>
                <div class="card-body pt-0 pb-5">
                    <div class="d-flex flex-column gap-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Сегодня</span>
                            <span class="text-gray-800">{{ $ordersToday }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Этот месяц</span>
                            <span class="text-gray-800">{{ $ordersThisMonth }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Мерчанты -->
        <div class="col-xl-3">
            <div class="card card-flush mb-5 mb-xl-10">
                <div class="card-header pt-5 pb-3">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ $totalMerchants }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Всего мерчантов</span>
                    </div>
                </div>
                <div class="card-body pt-0 pb-5">
                    <div class="d-flex flex-column gap-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Активных</span>
                            <span class="text-gray-800">{{ $activeMerchants }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Неактивных</span>
                            <span class="text-gray-800">{{ $totalMerchants - $activeMerchants }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Товары на складах -->
        <div class="col-xl-3">
            <div class="card card-flush mb-5 mb-xl-10">
                <div class="card-header pt-5 pb-3">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ number_format($totalStockQuantity, 0, ',', ' ') }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Товаров на складах</span>
                    </div>
                </div>
                <div class="card-body pt-0 pb-5">
                    <div class="d-flex flex-column gap-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Уникальных товаров</span>
                            <span class="text-gray-800">{{ $totalProducts }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Активных складов</span>
                            <span class="text-gray-800">{{ $totalWarehouses }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Доставки -->
        <div class="col-xl-3">
            <div class="card card-flush mb-5 mb-xl-10">
                <div class="card-header pt-5 pb-3">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ $totalDeliveries }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Всего доставок</span>
                    </div>
                </div>
                <div class="card-body pt-0 pb-5">
                    <div class="d-flex flex-column gap-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Доставлено</span>
                            <span class="text-gray-800">{{ $completedDeliveries }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-700 w-100">
                            <span>Доход</span>
                            <span class="text-gray-800">{{ money($totalDeliveryRevenue) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика по статусам заказов -->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Заказы по статусам</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Распределение заказов</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="d-flex flex-column gap-5">
                        @foreach(\App\Enums\OrderStatusEnum::cases() as $status)
                            @php
                                $count = $ordersByStatus[$status->value] ?? 0;
                                $percentage = $totalOrders > 0 ? round(($count / $totalOrders) * 100, 1) : 0;
                            @endphp
                            <div class="d-flex align-items-sm-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $status->label() }}</span>
                                            <span class="text-gray-500 fw-semibold d-block fs-7">{{ $count }} заказов ({{ $percentage }}%)</span>
                                        </div>
                                        <span class="badge badge-light-{{ $status->colorClass() }} fs-base">{{ $count }}</span>
                                    </div>
                                    <div class="progress h-6px w-100 mt-2">
                                        <div class="progress-bar bg-{{ $status->colorClass() }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние заказы -->
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Последние заказы</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">10 последних заказов</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.order.index') }}" class="btn btn-sm btn-light">Все заказы</a>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>ID</th>
                                <th>Мерчант</th>
                                <th>Статус</th>
                                <th>Дата</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('dashboard.order.show', $order) }}" class="text-gray-800 text-hover-primary fw-bold">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td>{{ $order->merchant->user->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $order->status->colorClass() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10">
                                        <span class="text-gray-500">Заказов пока нет</span>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
