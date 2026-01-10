@extends('layouts.dashboard')

@section('title', 'Транзакции')

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
            Транзакции
        </li>
    </ul>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-flush">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <h2>История транзакций</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <!-- Фильтры -->
            <div class="d-flex align-items-center gap-2 gap-md-3 mb-5">
                <form method="GET" action="{{ route('cabinet.transactions.index') }}" class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-solid w-250px ps-12" 
                               placeholder="Поиск по ID" 
                               value="{{ request('search') }}">
                    </div>
                    <select name="type" class="form-select form-select-solid w-150px" onchange="this.form.submit()">
                        <option value="">Все типы</option>
                        @foreach(\App\Enums\BalanceTransactionTypeEnum::cases() as $type)
                            @php
                                $typeLabels = [
                                    'top_up' => 'Пополнение',
                                    'initial_top_up' => 'Первичное пополнение',
                                    'withdrawal' => 'Снятие',
                                    'delivery_payment' => 'Оплата доставки',
                                    'delivery_reserved' => 'Резерв доставки',
                                    'refund' => 'Возврат',
                                ];
                            @endphp
                            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                {{ $typeLabels[$type->value] ?? $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('cabinet.transactions.index') }}" class="btn btn-sm btn-light">Сбросить</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">ID</th>
                        <th class="min-w-150px">Тип</th>
                        <th class="min-w-150px">Связанная доставка</th>
                        <th class="text-end min-w-150px">Сумма</th>
                        <th class="text-center min-w-150px">Дата</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="text-gray-800 fw-bold">{{ $transaction->id }}</span>
                            </td>
                            <td>
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
                                @endphp
                                <span class="badge badge-light-{{ $transaction->type->colorClass() }} fw-bold px-4 py-3">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $deliveryRelatedTypes = ['delivery_payment', 'delivery_reserved', 'refund'];
                                @endphp
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
                            <td colspan="5" class="text-center text-gray-500 py-10">
                                Транзакций не найдено
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="d-flex justify-content-end mt-5">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

