@extends('layouts.dashboard')

@section('title', 'Create order')

@section('content')

    <form id="orderForm" action="{{ route('dashboard.order.store') }}" method="POST" class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
        @csrf
        <div class="w-100 flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Order Details</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-5">
                        <!-- Merchant -->
                        <div class="fv-row fv-plugins-icon-container">
                            <label class="required form-label">Merchant</label>
                            <select id="merchant_id" class="form-select mb-2"
                                    name="merchant_id"
                                    data-control="select2"
                                    data-placeholder="Choose a merchant..."
                                    required>
                                <option value=""></option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->uuid }}"
                                        {{ old('merchant_id', request()->route('merchant.uuid')) === $merchant->uuid ? 'selected' : '' }}>
                                        {{ $merchant->company }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7">This merchant will own the order.</div>
                        </div>

                        <!-- Tariff -->
                        <div class="fv-row fv-plugins-icon-container">
                            <label class="required form-label">Tariff</label>
                            <select id="rate_id" class="form-select mb-2"
                                    name="tariff_id"
                                    data-control="select2"
                                    data-placeholder="Choose a tariff..."
                                    required>
                                <option value=""></option>
                                @foreach($tariffs as $tariff)
                                    <option value="{{ $tariff->id }}" {{ old('tariff_id') == $tariff->id ? 'selected' : '' }}>
                                        {{ $tariff->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7">Set the date of the order to process.</div>
                        </div>

                        <div class="fv-row fv-plugins-icon-container">
                            <label class="required form-label">Zone</label>
                            <select id="zone_id" class="form-select mb-2"
                                    name="zone_id"
                                    data-control="select2"
                                    data-placeholder="Choose a zone..."
                                    required>
                                <option value=""></option>
                                @if(old('zone_id'))
                                    <option value="{{ old('zone_id') }}" selected>{{ old('zone_name', 'Selected Zone') }}</option>
                                @endif
                            </select>
                            <div class="text-muted fs-7">Choose delivery zone for this tariff.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
            <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                <div class="card-header">
                    <div class="card-title"><h2>Order Items</h2></div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                            <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>Product</th>
                                <th class="text-end">SKU</th>
                                <th class="text-end">Stock</th>
                                <th class="text-end">Qty</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            <tr class="loader-row d-none">
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
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
                        <h2>Delivery Details</h2>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-5 gap-md-7">
                        <div class="d-flex flex-column flex-md-row gap-5">
                            <div class="flex-row-fluid">
                                <label class="form-label required">Address</label>
                                <input class="form-control @error('recipient_address') is-invalid @enderror"
                                       name="recipient_address"
                                       placeholder="Delivery address"
                                       value="{{ old('recipient_address') }}">
                                @error('recipient_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                                <label class="required form-label">Name</label>
                                <input class="form-control @error('recipient_name') is-invalid @enderror"
                                       name="recipient_name"
                                       placeholder="Recipient name"
                                       value="{{ old('recipient_name') }}">
                                @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                                <label class="required form-label">Phone</label>
                                <input class="form-control @error('recipient_phone') is-invalid @enderror"
                                       id="recipient_phone"
                                       name="recipient_phone"
                                       placeholder="Recipient phone"
                                       value="{{ old('recipient_phone') }}">
                                @error('recipient_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="fv-row fv-plugins-icon-container">
                            <label class="form-label">Note</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes"
                                      rows="2"
                                      placeholder="Optional note about the order">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-2">
                <a href="{{route('dashboard.order.index')}}" class="btn btn-light me-5">Cancel</a>
                <button type="submit" class="btn btn-primary"><span class="indicator-label">Create Order</span></button>
            </div>
        </div>

    </form>

@endsection

@push('custom_js')
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            Inputmask({
                "mask" : "(999) 999 99 99"
            }).mask("#recipient_phone");


            const zonesUrlTemplate = "{{ route('dashboard.delivery-rates.zones', ['id' => ':id']) }}";
            const productsUrlTemplate = "{{ route('dashboard.merchant.products', ['merchant' => ':uuid']) }}";
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
                        $('#zone_id').html('<option value="">Loading...</option>').prop('disabled', true).trigger('change');
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
            function loadProducts(merchantUuid, oldData = {}) {
                let productsUrl = productsUrlTemplate.replace(':uuid', merchantUuid);
                tbody.find('.loader-row').removeClass('d-none');
                tbody.find('tr:not(.loader-row)').remove();

                $.get(productsUrl, function(response) {
                    tbody.find('.loader-row').addClass('d-none');
                    tbody.html(response.html); // Используем готовый HTML

                    // Инициализация подсказок
                    $('[title]').tooltip();
                });
            }


            // Подгрузка товаров при выборе мерчанта
            $('#merchant_id').on('change', function() {
                let merchantUuid = $(this).val();
                if (!merchantUuid) return;
                loadProducts(merchantUuid);
            });

            // При загрузке страницы с ошибками валидации
            @if(old('merchant_id'))
            let oldMerchantUuid = '{{ old('merchant_id') }}';
            if (oldMerchantUuid) {
                loadProducts(oldMerchantUuid, oldProducts);

                // Загрузка зон если был выбран тариф
                @if(old('tariff_id'))
                $('#rate_id').trigger('change');
                @endif
            }
            @endif
        });
    </script>
@endpush
