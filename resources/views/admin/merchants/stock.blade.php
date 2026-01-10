@extends('layouts.dashboard')

@section('title', 'Stock - ' . $merchant->user->name)

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
            <a href="{{route('dashboard.merchants.index')}}" class="text-muted text-hover-primary">
                Merchants
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.merchants.show', $merchant)}}" class="text-muted text-hover-primary">
                {{$merchant->user->name}}
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Stock
        </li>
    </ul>
@endsection

@section('content')
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
                           data-kt-ecommerce-product-filter="search"
                           class="form-control form-control-solid w-250px ps-12"
                           placeholder="Поиск товара" />
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <table id="kt_ecommerce_products_table" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-200px">Product</th>
                    <th class="text-center min-w-100px">SKU</th>
                    <th class="text-center min-w-70px">Warehouse/Qty</th>
                    <th class="text-center min-w-100px">Total Qty</th>
                    <th class="text-end min-w-70px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsData as $data)
                    <tr>
                        <td>
                            <p class="text-gray-800 fs-5 fw-bold mb-0"
                               data-kt-ecommerce-product-filter="product_name">
                                {{ $data['product']->name }}
                            </p>
                        </td>
                        <td class="text-center">{{ $data['product']->sku }}</td>
                        <td class="text-center">
                            @foreach($data['warehouses'] as $warehouse => $qty)
                                <div>{{ $warehouse }}: <span class="fw-bold">{{ $qty }}</span></div>
                            @endforeach
                        </td>
                        <td class="text-center fw-bold">
                            {{ array_sum($data['warehouses']) }}
                        </td>
                        <td class="text-end">buttons</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        <!--end::Card body-->
    </div>
@endsection

@push('custom_js')
    <script src="{{asset('assets/js/plugins/datatables.bundle.js')}}"></script>
    <script src="{{asset('assets/js/custom/merchants/products.js')}}"></script>
@endpush
