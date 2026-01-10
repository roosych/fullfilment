@extends('layouts.dashboard')

@section('title', 'Товары')

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
            Товары
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
                    <form method="GET" action="{{ route('cabinet.products.index') }}" class="d-flex align-items-center gap-2">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control form-control-solid w-250px ps-12"
                               placeholder="Поиск товара" />
                        @if(request('search'))
                            <a href="{{ route('cabinet.products.index') }}" class="btn btn-sm btn-light">Сбросить</a>
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
                        <th class="min-w-200px">Товар</th>
                        <th class="text-center min-w-100px">SKU</th>
                        <th class="text-center min-w-100px">Штрихкод</th>
                        <th class="text-center min-w-100px">Доступно</th>
                        <th class="text-center min-w-100px">Зарезервировано</th>
                        <th class="text-center min-w-100px">Всего</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($products as $item)
                        @php $product = $item['product']; @endphp
                        <tr>
                            <td>
                                <p class="text-gray-800 fs-5 fw-bold mb-0">
                                    {{ $product->name }}
                                </p>
                                @if($product->description)
                                    <p class="text-gray-500 fs-7 mb-0">
                                        {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                    </p>
                                @endif
                            </td>
                            <td class="text-center">{{ $product->sku ?? '—' }}</td>
                            <td class="text-center">{{ $product->barcode ?? '—' }}</td>
                            <td class="text-center">
                                <span class="fw-bold text-{{ $item['available_stock'] > 0 ? 'success' : 'danger' }}">
                                    {{ $item['available_stock'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-warning">
                                    {{ $item['reserved_stock'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-gray-800">
                                    {{ $item['total_stock'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{route('cabinet.products.show', $product)}}" class="btn btn-icon btn-sm btn-light">
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
                            <td colspan="7" class="text-center text-gray-500 py-10">
                                Товаров не найдено
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

