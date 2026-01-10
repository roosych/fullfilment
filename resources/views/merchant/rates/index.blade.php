@extends('layouts.dashboard')

@section('title', 'Тарифы и цены')

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
            Тарифы и цены
        </li>
    </ul>
@endsection

@section('content')
    <div class="card card-flush mb-5 mb-xl-10">
        <div class="card-header">
            <div class="card-title">
                <h2>Тарифная сетка доставки</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <p class="text-gray-600 mb-7">
                Здесь представлены все доступные тарифы и цены на доставку. Цены указаны в зависимости от веса груза и зоны доставки.
            </p>

            <div class="row g-5 g-xl-10">
                @forelse($deliveryRates as $rate)
                    <div class="col-xl-6">
                        <div class="card card-flush h-md-100 mb-5 mb-lg-10">
                            <div class="card-header">
                                <div class="card-title flex-column pt-3">
                                    <h3 class="fw-bold mb-1">{{ $rate->name }}</h3>
                                    @if($rate->description)
                                        <div class="fs-6 text-gray-500">{{ $rate->description }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body py-2">
                                <div class="flex-grow-1">
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
                                            @php
                                                $groupedPrices = $rate->ratePrices->groupBy('delivery_zone_id');
                                            @endphp
                                            @forelse($groupedPrices as $zoneId => $prices)
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
                                                                {{ number_format($price->min_weight, 0, ',', ' ') }} – {{ number_format($price->max_weight, 0, ',', ' ') }} г<br>
                                                            @elseif(!is_null($price->min_weight))
                                                                ≥ {{ number_format($price->min_weight, 0, ',', ' ') }} г<br>
                                                            @elseif(!is_null($price->max_weight))
                                                                ≤ {{ number_format($price->max_weight, 0, ',', ' ') }} г<br>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td class="pt-4 text-gray-800 fw-bold">
                                                        @foreach($prices as $price)
                                                            {{ money($price->price) }}<br>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-6 text-muted">
                                                        Нет доступных зон для этого тарифа
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
                @empty
                    <div class="col-xl-12">
                        <div class="alert alert-info">
                            Тарифные планы временно недоступны. Пожалуйста, обратитесь к администратору.
                        </div>
                    </div>
                @endforelse
            </div>

            @if($deliveryRates->isNotEmpty())
                <div class="separator separator-dashed my-10"></div>
                
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                    <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-800 fw-bold mb-1">Дополнительная информация</h4>
                            <div class="fs-6 text-gray-600">
                                <ul class="mb-0 ps-4">
                                    <li>Цены указаны за доставку в зависимости от веса и зоны</li>
                                    <li>Окончательная стоимость может быть рассчитана при создании заказа</li>
                                    <li>Для уточнения деталей обращайтесь к администратору</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

