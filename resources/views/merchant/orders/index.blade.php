@extends('layouts.dashboard')

@section('title', 'Доставки')

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
            Доставки
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

    <div class="card card-flush">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <form method="GET" action="{{ route('cabinet.orders.index') }}" class="d-flex align-items-center gap-2">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control form-control-solid w-250px ps-12"
                               placeholder="Поиск доставки" />
                        <select class="form-select form-select-solid w-150px"
                                name="status"
                                onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            @foreach(\App\Enums\OrderStatusEnum::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('search') || request('status'))
                            <a href="{{ route('cabinet.orders.index') }}" class="btn btn-sm btn-light">Сбросить</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-100px">ID</th>
                        <th class="min-w-150px">Получатель</th>
                        <th class="min-w-150px">Адрес</th>
                        <th class="text-center min-w-100px">Статус</th>
                        <th class="text-center min-w-100px">Товары / Кол-во</th>
                        <th class="text-center min-w-100px">Дата создания</th>
                        <th class="text-center min-w-100px">Статус доставки</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($orders as $order)
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
                            <td>
                                <span class="text-gray-800">{{$order->recipient_address}}</span>
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
                            <td class="text-center">
                                @if($order->delivery)
                                    <div class="badge badge-light-{{ $order->delivery->status->colorClass() }}">
                                        {{ $order->delivery->status->label() }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Не создана</span>
                                @endif
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
                            <td colspan="8" class="text-center text-gray-500 py-10">
                                Доставок не найдено
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="d-flex justify-content-end mt-5">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

