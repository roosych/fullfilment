@extends('layouts.dashboard')

@section('title', 'Пополнения склада')

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
            Пополнения склада
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
                <h2>Пополнения склада</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <!-- Фильтры -->
            <div class="d-flex align-items-center gap-2 gap-md-3 mb-5">
                <form method="GET" action="{{ route('cabinet.stock.index') }}" class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-solid w-250px ps-12" 
                               placeholder="Поиск по коду партии" 
                               value="{{ request('search') }}">
                    </div>
                    @if($warehouses->count() > 0)
                        <select name="warehouse_id" class="form-select form-select-solid w-150px" onchange="this.form.submit()">
                            <option value="">Все склады</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @if(request()->hasAny(['search', 'warehouse_id']))
                        <a href="{{ route('cabinet.stock.index') }}" class="btn btn-sm btn-light">Сбросить</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Номер партии</th>
                        <th class="min-w-150px">Склад</th>
                        <th class="text-center min-w-100px">Товаров</th>
                        <th class="text-center min-w-100px">Количество</th>
                        <th class="text-center min-w-150px">Дата получения</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($batches as $batch)
                        <tr>
                            <td>
                                <a href="{{route('cabinet.stock.show', $batch)}}" class="text-gray-800 text-hover-primary fw-bold">
                                    {{$batch->batch_code}}
                                </a>
                            </td>
                            <td>
                                <span class="text-gray-800">{{$batch->warehouse->name ?? '—'}}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-gray-800">{{$batch->entries->count()}}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-gray-800">{{$batch->entries->sum('quantity')}}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($batch->received_at)->isoFormat('DD MMM Y, HH:mm') }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{route('cabinet.stock.show', $batch)}}" class="btn btn-icon btn-sm btn-light">
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
                                Пополнений склада пока нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($batches->hasPages())
                <div class="d-flex justify-content-end mt-5">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

