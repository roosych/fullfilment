@extends('layouts.dashboard')

@section('title', 'Тарифы доставки')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">Панель управления</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">Тарифы доставки</li>
    </ul>
@endsection


{{-- Если есть зоны, показываем кнопку "Add tariff" --}}
@if($zones->count() > 0)
    @section('actions')
        <div class="d-flex align-items-center gap-3 gap-lg-5">
            <a href="{{ route('dashboard.delivery-rates.create') }}" class="btn btn-sm btn-flex btn-center btn-dark px-4">
                <i class="ki-duotone ki-plus-square fs-2 p-0 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                Добавить тариф
            </a>
        </div>
    @endsection
@endif


@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif


    {{-- Если зон нет — предупреждение --}}
    @if($zones->count() === 0)
        <div class="col-xl-12">
            <div class="card mb-5 mb-xl-10">
                <div class="card-body">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Зоны доставки не найдены</h4>
                                <div class="fs-6 text-gray-700">
                                    Чтобы создать или назначить тарифы доставки, сначала нужно настроить хотя бы одну зону доставки.
                                    Пожалуйста, <a href="{{ route('dashboard.delivery-zones.index') }}" class="fw-bold text-hover-warning">
                                        создайте зону доставки</a> для продолжения.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Вывод тарифов --}}
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            @forelse($rates as $rate)
                <div class="col-xl-6">
                    <div class="card card-flush h-md-100 mb-5 mb-lg-10">
                        <div class="card-header">
                            <div class="card-title flex-column pt-3">
                                <h3 class="fw-bold mb-1">{{$rate->name}}</h3>
                                <div class="fs-6 text-gray-500">{{$rate->description}}</div>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{route('dashboard.delivery-rates.edit', $rate)}}" class="btn btn-light btn-sm">
                                    Просмотр
                                </a>
                            </div>
                        </div>

                        <div class="card-body py-2">
                            <div class="flex-grow-1">
                                <!--begin::Table-->
                                <div class="table-responsive border-bottom">
                                    <table class="table mb-3 align-middle">
                                        <thead>
                                        <tr class="border-bottom fs-6 fw-bold text-muted">
                                            <th class="min-w-175px pb-2">Зона</th>
                                            <th class="min-w-150px text-end pb-2">Вес</th>
                                            <th class="min-w-100px text-end pb-2">Цена</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @forelse($rate->ratePrices->groupBy('delivery_zone_id') as $zoneId => $prices)
                                            <tr class="fw-semibold text-gray-800 fs-5 text-end">
                                                <td class="d-flex align-items-center fw-bold text-start pt-4">
                                                    <i class="ki-duotone ki-abstract-7 text-primary fs-2 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $prices->first()->zone->name ?? 'Неизвестная зона' }}
                                                </td>
                                                <td class="pt-4">
                                                    @foreach($prices as $price)
                                                        @if(!is_null($price->min_weight) && !is_null($price->max_weight))
                                                            {{ number_format($price->min_weight) }} – {{ number_format($price->max_weight) }} g<br>
                                                        @elseif(!is_null($price->min_weight))
                                                            ≥ {{ number_format($price->min_weight) }} g<br>
                                                        @elseif(!is_null($price->max_weight))
                                                            ≤ {{ number_format($price->max_weight) }} g<br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="pt-4 text-gray-800 fw-bold">
                                                    @foreach($prices as $price)
                                                        {{ $price->price_formatted }} ₼<br>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-6 text-muted">
                                                    Для этого тарифа не назначены зоны доставки.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>

                                </div>
                                <!--end::Table-->
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-xl-12">
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-body">
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Тарифы не найдены</h4>
                                        <div class="fs-6 text-gray-700">
                                            Вы еще не создали ни одного тарифа доставки. Нажмите "Добавить тариф", чтобы создать один.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @endif
@endsection
