@extends('layouts.dashboard')

@section('title', 'Создать заказ доставки')

@section('content')

    <form id="orderForm" action="{{ route('cabinet.orders.store') }}" method="POST" class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
        @csrf
        <div class="w-100 flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Детали заказа</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-5">
                        <!-- Tariff -->
                        <div class="fv-row fv-plugins-icon-container">
                            <label class="required form-label">Тариф</label>
                            <select id="rate_id" class="form-select mb-2"
                                    name="tariff_id"
                                    data-control="select2"
                                    data-placeholder="Выберите тариф..."
                                    required>
                                <option value=""></option>
                                @foreach($tariffs as $tariff)
                                    <option value="{{ $tariff->id }}" {{ old('tariff_id') == $tariff->id ? 'selected' : '' }}>
                                        {{ $tariff->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7">Выберите тариф для доставки.</div>
                        </div>

                        <div class="fv-row fv-plugins-icon-container">
                            <label class="required form-label">Зона</label>
                            <select id="zone_id" class="form-select mb-2"
                                    name="zone_id"
                                    data-control="select2"
                                    data-placeholder="Выберите зону..."
                                    required>
                                <option value=""></option>
                                @if(old('zone_id'))
                                    <option value="{{ old('zone_id') }}" selected>{{ old('zone_name', 'Выбранная зона') }}</option>
                                @endif
                            </select>
                            <div class="text-muted fs-7">Выберите зону доставки для этого тарифа.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
            <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                <div class="card-header">
                    <div class="card-title"><h2>Товары</h2></div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                            <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>Товар</th>
                                <th class="text-end">SKU</th>
                                <th class="text-end">Остаток</th>
                                <th class="text-end">Количество</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            <tr class="loader-row d-none">
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Загрузка...</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Данные получателя</h2>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-5 gap-md-7">
                        <div class="d-flex flex-column flex-md-row gap-5">
                            <div class="flex-row-fluid">
                                <label class="form-label required">Адрес</label>
                                <input class="form-control @error('recipient_address') is-invalid @enderror"
                                       name="recipient_address"
                                       placeholder="Адрес доставки"
                                       value="{{ old('recipient_address') }}">
                                @error('recipient_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                                <label class="required form-label">Имя</label>
                                <input class="form-control @error('recipient_name') is-invalid @enderror"
                                       name="recipient_name"
                                       placeholder="Имя получателя"
                                       value="{{ old('recipient_name') }}">
                                @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                                <label class="required form-label">Телефон</label>
                                <input class="form-control @error('recipient_phone') is-invalid @enderror"
                                       id="recipient_phone"
                                       name="recipient_phone"
                                       placeholder="Телефон получателя"
                                       value="{{ old('recipient_phone') }}">
                                @error('recipient_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="fv-row fv-plugins-icon-container">
                            <label class="form-label">Примечание</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes"
                                      rows="2"
                                      placeholder="Дополнительная информация о заказе">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-2">
                <a href="{{route('cabinet.orders.index')}}" id="cancelBtn" class="btn btn-light me-5">Отмена</a>
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <span class="indicator-label">
                        Создать заказ
                    </span>
                    <span class="indicator-progress d-none">
                        Создается...
                        <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>
                    </span>
                </button>
            </div>
        </div>

    </form>

@endsection

@push('custom_js')
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Ошибка...',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            Inputmask({
                "mask" : "(999) 999 99 99"
            }).mask("#recipient_phone");

            const zonesUrlTemplate = "{{ url('cabinet/orders/rates') }}/:id/zones";
            const productsUrlTemplate = "{{ route('cabinet.orders.products.list') }}";
            const tbody = $('tbody.fw-semibold');

            // Сохраняем old products из Laravel
            const oldProducts = @json(old('products', []));

            // Зона по умолчанию дизейбл
            $('#zone_id').prop('disabled', true);

            // Подгрузка зон при выборе тарифа
            $('#rate_id').on('change', function () {
                let rateId = $(this).val();
                if (!rateId) {
                    $('#zone_id').prop('disabled', true).val('').trigger('change');
                    return;
                }

                let url = zonesUrlTemplate.replace(':id', rateId);
                $.ajax({
                    url: url,
                    method: 'GET',
                    beforeSend: function () {
                        $('#zone_id').html('<option value="">Загрузка...</option>').prop('disabled', true).trigger('change');
                    },
                    success: function (response) {
                        let zoneSelect = $('#zone_id');
                        zoneSelect.empty().append('<option value=""></option>');

                        response.zones.forEach(zone => {
                            let selected = '{{ old("zone_id") }}' == zone.id ? 'selected' : '';
                            zoneSelect.append(`<option value="${zone.id}" ${selected}>${zone.name}</option>`);
                        });

                        zoneSelect.prop('disabled', false).trigger('change');
                    }
                });
            });

            // Функция для загрузки товаров
            function loadProducts(oldData = {}) {
                tbody.find('.loader-row').removeClass('d-none');
                tbody.find('tr:not(.loader-row)').remove();

                $.get(productsUrlTemplate, function(response) {
                    tbody.find('.loader-row').addClass('d-none');
                    tbody.html(response.html); // Используем готовый HTML

                    // Инициализация подсказок
                    $('[title]').tooltip();
                });
            }

            // Загружаем товары при загрузке страницы
            loadProducts();

            // При загрузке страницы с ошибками валидации
            @if(old('tariff_id'))
            $('#rate_id').trigger('change');
            @endif

            // Обработчик отправки формы с индикатором загрузки
            $('#orderForm').on('submit', function(e) {
                const $submitBtn = $('#submitBtn');
                const $cancelBtn = $('#cancelBtn');
                
                // Блокируем кнопку отправки и меняем содержимое на текст с крутилкой
                $submitBtn.prop('disabled', true);
                $submitBtn.html('Создается... <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>');
                
                // Блокируем кнопку отмены
                $cancelBtn.addClass('disabled').css('pointer-events', 'none').css('opacity', '0.6');
            });
        });
    </script>
@endpush

