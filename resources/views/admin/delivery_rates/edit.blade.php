@extends('layouts.dashboard')

@section('title', 'Редактировать тариф доставки')

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    @if ($errors->any())
        <x-alert type="danger" :message="implode('<br>', $errors->all())"/>
    @endif

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-8">
            <form action="{{ route('dashboard.delivery-rates.update', $deliveryRate) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Tariff title --}}
                <div class="row mb-7">
                    <label class="col-md-3 col-form-label text-md-end required">Название</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control" value="{{ old('name', $deliveryRate->name) }}" required>
                    </div>
                </div>

                {{-- Description --}}
                <div class="row mb-7">
                    <label class="col-md-3 col-form-label text-md-end">Описание</label>
                    <div class="col-md-9">
                        <input type="text" name="description" class="form-control" value="{{ old('description', $deliveryRate->description) }}">
                    </div>
                </div>

                {{-- Zones --}}
                @foreach($zones as $zone)
                    <div class="row mb-2">
                        <div class="col-md-9 offset-md-3">
                            <h5>{{ $zone->name }}</h5>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-12">
                            <div id="zone-{{ $zone->id }}" class="repeater">
                                <div data-repeater-list="zones[{{ $zone->id }}]">
                                    @php
                                        $oldIntervals = old("zones.{$zone->id}");
                                        $existingIntervals = $deliveryRate->ratePrices->where('delivery_zone_id', $zone->id)->toArray();
                                        $intervals = $oldIntervals ?? ($existingIntervals ?: [[]]);
                                    @endphp

                                    @foreach($intervals as $interval)
                                        <div data-repeater-item class="row mb-3">
                                            <div class="col-md-3">
                                                <input type="number" step="1" name="min_weight" class="form-control" placeholder="Мин. вес (г)" value="{{ $interval['min_weight'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="1" name="max_weight" class="form-control" placeholder="Макс. вес (г)" value="{{ $interval['max_weight'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="0.01" name="price" class="form-control" placeholder="Цена (₼)" value="{{ isset($interval['price']) ? $interval['price'] / 100 : '' }}">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <input type="checkbox" name="active" class="form-check-input me-2" {{ isset($interval['active']) && $interval['active'] ? 'checked' : '' }}> Активен
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" data-repeater-delete class="btn btn-light-danger">Удалить</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="button" data-repeater-create class="btn btn-light-primary">
                                        <i class="ki-duotone ki-plus fs-3"></i> Добавить диапазон
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator my-4"></div>
                @endforeach

                {{-- Submit --}}
                <div class="row py-5">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary">Обновить</button>
                        <button type="reset" class="btn btn-light">Отмена</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('custom_js')
    <script src="{{ asset('assets/js/plugins/formrepeater.bundle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @foreach($zones as $zone)
            $('#zone-{{ $zone->id }}').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });
            @endforeach
        });
    </script>
@endpush
