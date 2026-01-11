@extends('layouts.dashboard')

@section('title', 'Новый тариф доставки')

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    @if ($errors->any())
        <x-alert type="danger" :message="implode('<br>', $errors->all())"/>
    @endif

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-8">
            <form id="deliveryRateForm" action="{{ route('dashboard.delivery-rates.store') }}" method="POST">
                @csrf

                {{-- Tariff title --}}
                <div class="row mb-7">
                    <label class="col-md-3 col-form-label text-md-end required">Название</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>

                {{-- Description --}}
                <div class="row mb-7">
                    <label class="col-md-3 col-form-label text-md-end">Описание</label>
                    <div class="col-md-9">
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
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
                                        $intervals = old("zones.{$zone->id}") ?? [[]];
                                    @endphp

                                    @foreach($intervals as $interval)
                                        <div data-repeater-item class="row mb-3">
                                            <div class="col-md-3">
                                                <input type="number" step="1"
                                                       name="zones[{{ $zone->id }}][{{ $loop->index }}][min_weight]"
                                                       class="form-control"
                                                       placeholder="Мин. вес (г)"
                                                       value="{{ old("zones.{$zone->id}.{$loop->index}.min_weight", $interval['min_weight'] ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="1"
                                                       name="zones[{{ $zone->id }}][{{ $loop->index }}][max_weight]"
                                                       class="form-control"
                                                       placeholder="Макс. вес (г)"
                                                       value="{{ old("zones.{$zone->id}.{$loop->index}.max_weight", $interval['max_weight'] ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="0.01"
                                                       name="zones[{{ $zone->id }}][{{ $loop->index }}][price]"
                                                       class="form-control"
                                                       placeholder="Цена (₼)"
                                                       value="{{ old("zones.{$zone->id}.{$loop->index}.price", isset($interval['price']) ? $interval['price'] / 100 : '') }}">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <input type="hidden"
                                                       name="zones[{{ $zone->id }}][{{ $loop->index }}][active]"
                                                       value="0">
                                                <input type="checkbox"
                                                       name="zones[{{ $zone->id }}][{{ $loop->index }}][active]"
                                                       class="form-check-input me-2"
                                                       value="1"
                                                    {{ old("zones.{$zone->id}.{$loop->index}.active", $interval['active'] ?? true) ? 'checked' : '' }}> Активен
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
                        <a href="{{ route('dashboard.delivery-rates.index') }}" id="cancelBtn" class="btn btn-light me-3">Отмена</a>
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <span class="indicator-label">
                                Создать
                            </span>
                            <span class="indicator-progress d-none">
                                Создание...
                                <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
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

            // Обработчик отправки формы с индикатором загрузки
            $('#deliveryRateForm').on('submit', function(e) {
                const $submitBtn = $('#submitBtn');
                const $cancelBtn = $('#cancelBtn');
                
                // Блокируем кнопку отправки и меняем содержимое на текст с крутилкой
                $submitBtn.prop('disabled', true);
                $submitBtn.html('Создание... <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>');
                
                // Блокируем кнопку отмены
                $cancelBtn.addClass('disabled').css('pointer-events', 'none').css('opacity', '0.6');
            });
        });
    </script>
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Упс...',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif
@endpush
