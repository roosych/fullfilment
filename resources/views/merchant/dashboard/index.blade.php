@extends('layouts.dashboard')

@section('title', 'Главная')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('cabinet.dashboard')}}" class="text-muted text-hover-primary">
                Главная
            </a>
        </li>
    </ul>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <!-- Статистика -->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ $ordersCount }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Всего доставок</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-primary me-2 lh-1 ls-n2">{{ $activeOrdersCount }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Активные доставки</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-success me-2 lh-1 ls-n2">{{ $completedOrdersCount }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Завершенные</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card card-flush">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <span class="fs-2hx fw-bold text-info me-2 lh-1 ls-n2">{{ $productsCount }}</span>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Товаров</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация о балансе -->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Баланс</h2>
                    </div>
                </div>
                <div class="card-body pt-0 d-flex flex-column">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-600">Доступный баланс:</span>
                            <span class="fs-3 fw-bold text-gray-800">{{ money($merchant->balance) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-gray-600">Зарезервировано:</span>
                            <span class="fs-3 fw-bold text-warning">{{ money($merchant->reserved_balance) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Остатки</h2>
                    </div>
                </div>
                <div class="card-body pt-0 d-flex flex-column">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-600">Общий остаток товаров:</span>
                            <span class="fs-3 fw-bold text-gray-800">{{ $totalStock }} шт.</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-gray-600">Зарезервировано:</span>
                            <span class="fs-3 fw-bold text-warning">{{ $reservedStock }} шт.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Последние доставки -->
    <div class="card card-flush mb-5 mb-xl-10">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <h2>Последние доставки</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('cabinet.orders.index') }}" class="btn btn-sm btn-primary">
                    Все доставки
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-100px">ID</th>
                        <th class="min-w-150px">Получатель</th>
                        <th class="text-center min-w-100px">Статус</th>
                        <th class="text-center min-w-100px">Товары</th>
                        <th class="text-center min-w-100px">Дата создания</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                    <a href="{{route('cabinet.orders.show', $order)}}" class="text-gray-800 text-hover-primary fw-bold">
                                    #{{$order->id}}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold">{{$order->recipient_name}}</span>
                                    <span class="text-gray-500">{{$order->recipient_phone}}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="badge badge-light-{{ $order->status->colorClass() }}">
                                    {{ $order->status->label() }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-gray-800">
                                    {{$order->items->count()}} / {{ $order->items->sum('quantity') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($order->created_at)->isoFormat('DD MMM Y, HH:mm') }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{route('cabinet.orders.show', $order)}}" class="btn btn-icon btn-sm btn-light">
                                    <i class="ki-duotone ki-black-right fs-2 text-muted">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-10">
                                Доставок пока нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Последние транзакции -->
    <div class="card card-flush">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <h2>Последние транзакции</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('cabinet.transactions.index') }}" class="btn btn-sm btn-primary">
                    Все транзакции
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Тип</th>
                        <th class="min-w-150px">Связанная доставка</th>
                        <th class="text-end min-w-150px">Сумма</th>
                        <th class="text-center min-w-150px">Дата</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($recentTransactions as $transaction)
                        @php
                            $typeLabels = [
                                'top_up' => 'Пополнение',
                                'initial_top_up' => 'Первичное пополнение',
                                'withdrawal' => 'Снятие',
                                'delivery_payment' => 'Оплата доставки',
                                'delivery_reserved' => 'Резерв доставки',
                                'refund' => 'Возврат',
                            ];
                            $typeLabel = $typeLabels[$transaction->type->value] ?? $transaction->type->label();
                            $deliveryRelatedTypes = ['delivery_payment', 'delivery_reserved', 'refund'];
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-light-{{ $transaction->type->colorClass() }} fw-bold px-4 py-3">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td>
                                @if(in_array($transaction->type->value, $deliveryRelatedTypes) && $transaction->source instanceof \App\Models\Delivery)
                                    @php
                                        $delivery = $transaction->source;
                                        $order = $delivery->order;
                                    @endphp
                                    @if($order)
                                        <a href="{{ route('cabinet.orders.show', $order) }}" class="text-primary text-hover-primary fw-bold">
                                            Заказ #{{ $order->id }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold {{ $transaction->amount < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $transaction->amount < 0 ? '-' : '+' }}{{ money(abs($transaction->amount)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($transaction->created_at)->isoFormat('DD MMM Y, HH:mm') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-10">
                                Транзакций пока нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

