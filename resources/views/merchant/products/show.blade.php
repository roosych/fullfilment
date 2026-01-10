@extends('layouts.dashboard')

@section('title', $product->name)

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
            <a href="{{route('cabinet.products.index')}}" class="text-muted text-hover-primary">
                Товары
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            {{ $product->name }}
        </li>
    </ul>
@endsection

@section('content')
    <div class="d-flex flex-column gap-7 gap-lg-10">
        <!-- Информация о товаре -->
        <div class="card card-flush py-4 flex-row-fluid">
            <div class="card-header">
                <div class="card-title">
                    <h2>{{ $product->name }}</h2>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <div class="mb-5">
                            <label class="text-gray-600 fw-semibold">Название</label>
                            <div class="fs-5 fw-bold text-gray-800">{{ $product->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-5">
                            <label class="text-gray-600 fw-semibold">SKU</label>
                            <div class="fs-5 fw-bold text-gray-800">{{ $product->sku ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                @if($product->barcode)
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="text-gray-600 fw-semibold">Штрихкод</label>
                                <div class="fs-5 fw-bold text-gray-800">{{ $product->barcode }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($product->description)
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="mb-5">
                                <label class="text-gray-600 fw-semibold">Описание</label>
                                <div class="fs-5 text-gray-800">{{ $product->description }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-5">
                            <label class="text-gray-600 fw-semibold">Доступно</label>
                            <div class="fs-2hx fw-bold text-success">{{ $availableStock }} шт.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-5">
                            <label class="text-gray-600 fw-semibold">Зарезервировано</label>
                            <div class="fs-2hx fw-bold text-warning">{{ $reservedStock }} шт.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-5">
                            <label class="text-gray-600 fw-semibold">Всего</label>
                            <div class="fs-2hx fw-bold text-gray-800">{{ $totalStock }} шт.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Остатки по складам -->
        @if(count($stockByWarehouse) > 0)
            <div class="card card-flush py-4 flex-row-fluid">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Остатки по складам</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Склад</th>
                                <th class="text-center min-w-100px">Количество</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            @foreach($stockByWarehouse as $warehouseName => $quantity)
                                <tr>
                                    <td>
                                        <span class="text-gray-800 fw-bold">{{ $warehouseName }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-gray-800">{{ $quantity }} шт.</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

