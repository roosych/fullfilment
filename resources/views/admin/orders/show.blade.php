@extends('layouts.dashboard')

@section('title', 'Order #' .$order->id)

@section('content')
    <div class="d-flex flex-column gap-7 gap-lg-10">
        <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-lg-n2 me-auto" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4 active"
                       data-bs-toggle="tab"
                       href="#kt_ecommerce_sales_order_summary"
                       aria-selected="true" role="tab">
                        Сводка заказа
                    </a>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4"
                       data-bs-toggle="tab"
                       href="#kt_ecommerce_sales_order_history"
                       aria-selected="false" role="tab">
                        История заказа
                    </a>
                </li>
            </ul>

            @if(in_array($order->status, [
                \App\Enums\OrderStatusEnum::CREATED,
                \App\Enums\OrderStatusEnum::READY_FOR_DELIVERY,
            ]))
                <form action="{{ route('dashboard.order.cancel', $order) }}" method="post">
                    @csrf
                    <button class="btn btn-danger">Отменить заказ</button>
                </form>
            @endif

        </div>

        <div class="tab-content">
            <div class="tab-pane fade active show" id="kt_ecommerce_sales_order_summary" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                        <!-- Payment / Merchant Info -->
                        <div class="card card-flush py-4 flex-row-fluid position-relative">
                            <div class="position-absolute top-0 end-0 bottom-0 opacity-10 d-flex align-items-center me-5">
                                <i class="ki-duotone ki-information-2" style="font-size: 14em">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>

                            <div class="card-header">
                                <div class="card-title"><h2>Информация</h2></div>
                            </div>

                            <div class="card-body pt-0">
                                <div>
                                    Мерчант: <span class="fs-5 fw-bold text-gray-800">
                                    {{$order->merchant->user->name ?? 'не указан'}} <span class="ms-3">{{$order->merchant->user->phone ?? 'не указан'}}</span>
                                </span>
                                </div>
                                <div>
                                    Тариф: <span class="fs-5 fw-bold text-gray-800">{{$order->rate->name ?? 'не указан'}}</span>
                                </div>
                                <div>
                                    Зона: <span class="fs-5 fw-bold text-gray-800">{{$order->zone->name ?? 'не указана'}}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Info -->
                        <div class="card card-flush py-4 flex-row-fluid position-relative">
                            <div class="position-absolute top-0 end-0 bottom-0 opacity-10 d-flex align-items-center me-5">
                                <i class="ki-duotone ki-delivery" style="font-size: 13em">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>

                            <div class="card-header">
                                <div class="card-title"><h2>Информация о доставке</h2></div>
                            </div>

                            <div class="card-body pt-0">
                                <div>
                                    Получатель: <span class="fs-5 fw-bold text-gray-800">
                                    {{$order->recipient_name ?? 'не указан'}} <span class="ms-3">{{$order->recipient_phone ?? 'не указан'}}</span>
                                </span>
                                </div>
                                <div>
                                    Адрес: <span class="fs-5 fw-bold text-gray-800">{{$order->recipient_address}}</span>
                                </div>
                                <div>
                                    Примечания: <span class="fs-5 fw-bold text-gray-800">{{$order->notes}}</span>
                                </div>

                                @if($order->activeDelivery)
                                    <div>
                                        Цена:
                                        <span class="fs-5 fw-bold text-gray-800">
                                        {{ money($order->activeDelivery->price) }}
                                    </span>
                                    </div>

                                    <div>
                                        Вес:
                                        <span class="fs-5 fw-bold text-gray-800 ms-2">
                                        {{ (int)$order->activeDelivery->weight }} гр.
                                    </span>
                                    </div>

                                    @if($order->activeDelivery->status->is(\App\Enums\DeliveryStatusEnum::CREATED))
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-primary start-delivery-btn"
                                                    data-delivery-id="{{ $order->activeDelivery->id }}"
                                                    data-url="{{ route('dashboard.deliveries.start', $order->activeDelivery) }}">
                                                Отправить в путь
                                            </button>
                                            <button class="btn btn-sm btn-success ms-2 complete-delivery-btn"
                                                    data-delivery-id="{{ $order->activeDelivery->id }}"
                                                    data-url="{{ route('dashboard.deliveries.complete', $order->activeDelivery) }}"
                                                    title="Завершить без списания">
                                                Завершить
                                            </button>
                                            <button class="btn btn-sm btn-danger ms-2 cancel-delivery-btn"
                                                    data-delivery-id="{{ $order->activeDelivery->id }}"
                                                    data-url="{{ route('dashboard.deliveries.cancel', $order->activeDelivery) }}">
                                                Отменить
                                            </button>
                                        </div>
                                    @elseif($order->activeDelivery->status->is(\App\Enums\DeliveryStatusEnum::ON_THE_WAY))
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-success complete-delivery-btn"
                                                    data-delivery-id="{{ $order->activeDelivery->id }}"
                                                    data-url="{{ route('dashboard.deliveries.complete', $order->activeDelivery) }}"
                                                    title="Завершить и списать с мерчанта">
                                                Отметить как доставлено (Списать)
                                            </button>
                                            <button class="btn btn-sm btn-danger ms-2 fail-delivery-btn"
                                                    data-delivery-id="{{ $order->activeDelivery->id }}"
                                                    data-url="{{ route('dashboard.deliveries.fail', $order->activeDelivery) }}">
                                                Отметить как неудачную
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Order #{{$order->id}}</h2>
                                <div class="ms-3 fs-6 badge badge-light-{{$order->status->colorClass()}}">{{$order->status->label()}}</div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                    <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-175px">Товар</th>
                                        <th class="min-w-100px text-end">Артикул</th>
                                        <th class="w-150px text-end">Кол-во</th>
                                    </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <p class="fw-bold text-gray-800 mb-0">
                                                            {{ $item->product->name }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $item->product->sku }}</td>
                                            <td class="text-end">{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach

                                    @if($order->status->is(\App\Enums\OrderStatusEnum::CREATED))
                                        <tr>
                                            <td colspan="2" class="fs-5 text-gray-800 text-end">Общий вес (гр.)</td>
                                            <td class="text-gray-800 fs-5 fw-bolder text-end">
                                                <input name="weight_input" id="weight_input" type="number" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fs-3 text-gray-800 text-end">Стоимость доставки</td>
                                            <td class="text-gray-800 fs-3 fw-bolder text-end">
                                                <span id="delivery_price">0.00</span>₼
                                            </td>
                                        </tr>
                                    @elseif($order->activeDelivery)
                                        <tr>
                                            <td colspan="2" class="fs-3 text-gray-800 text-end">Стоимость доставки</td>
                                            <td class="text-gray-800 fs-3 fw-bolder text-end">
                                                <span id="delivery_price">{{ money($order->activeDelivery->price) }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                @if($order->status->is(\App\Enums\OrderStatusEnum::CREATED) && !$order->activeDelivery)
                                    <div class="d-flex justify-content-end mt-4">
                                        <form id="delivery_form" action="{{ route('dashboard.deliveries.store', $order->uuid) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="weight_input" id="weight_input_hidden">
                                            <button type="submit" class="btn btn-primary" id="create_delivery_btn">Создать доставку</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order History Tab -->
            <div class="tab-pane fade" id="kt_ecommerce_sales_order_history" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="card card-flush py-4 flex-row-fluid">
                        <div class="card-header">
                            <div class="card-title"><h2>История заказа</h2></div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive"></div>
                        </div>
                    </div>
                    <div class="card card-flush py-4 flex-row-fluid">
                        <div class="card-header">
                            <div class="card-title"><h2>Данные заказа</h2></div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_js')
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Упс...',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif

    <script>
        $(function() {
            const $weightInput = $('#weight_input');
            const $deliveryBtn = $('#create_delivery_btn');
            const $deliveryPrice = $('#delivery_price');
            const rateId = '{{ $order->delivery_rate_id }}';
            const zoneId = '{{ $order->delivery_zone_id }}';
            let timer;

            @if($order->status->is(\App\Enums\OrderStatusEnum::CREATED) && !$order->activeDelivery)
            $weightInput.on('input', function() {
                clearTimeout(timer);
                const weight = parseInt($(this).val());
                $('#weight_input_hidden').val(weight);
                $deliveryBtn.prop('disabled', true);

                if(!weight || weight <= 0) {
                    $deliveryPrice.text('0.00');
                    return;
                }

                timer = setTimeout(() => {
                    $.ajax({
                        url: "{{ route('dashboard.delivery.calculatePrice') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            weight: weight,
                            rate_id: rateId,
                            zone_id: zoneId
                        },
                        success: function(response) {
                            if(response.success) {
                                $deliveryPrice.text(response.price_formatted);
                                $deliveryBtn.prop('disabled', false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    allowOutsideClick: false
                                }).then(() => {
                                    const maxWeight = parseInt(response.message.match(/\d+/)[0]);
                                    $weightInput.val(maxWeight);
                                    $deliveryPrice.text(response.price_formatted ? response.price_formatted : '0.00');
                                    $deliveryBtn.prop('disabled', false);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                }, 500);
            });
            @endif

            // Отправка формы
            $('#delivery_form').on('submit', function(e) {
                const weight = parseInt($weightInput.val());
                if(!weight || weight <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Введите корректный вес для создания доставки.',
                        allowOutsideClick: false
                    });
                    return false;
                }
                $('#create_delivery_btn').prop('disabled', true);
                applyWait($('body'));
            });

            @if($order->activeDelivery)
            const deliveryId = {{ $order->activeDelivery->id }};
            const deliveryStatus = '{{ $order->activeDelivery->status }}';

            $(document).on('click', '.start-delivery-btn', function(e){
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url');
                Swal.fire({
                    title: 'Отправить доставку в путь?',
                    html: '<p class="mb-2">Доставка будет переведена в статус "В пути"</p>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Да, отправить',
                    showLoaderOnConfirm: true,
                    preConfirm: () => $.post(url, {_token: "{{ csrf_token() }}"})
                }).then((result) => { if(result.isConfirmed){ location.reload(); } });
            });

            $(document).on('click', '.complete-delivery-btn', function(e){
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url');
                Swal.fire({
                    title: 'Завершить доставку?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Да, завершить',
                    showLoaderOnConfirm: true,
                    preConfirm: () => $.post(url, {_token: "{{ csrf_token() }}"})
                }).then((result) => { if(result.isConfirmed){ location.reload(); } });
            });

            $(document).on('click', '.cancel-delivery-btn', function(e){
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url');
                Swal.fire({
                    title: 'Отменить доставку?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Да, отменить',
                    showLoaderOnConfirm: true,
                    preConfirm: () => $.post(url, {_token: "{{ csrf_token() }}"})
                }).then((result) => { if(result.isConfirmed){ location.reload(); } });
            });

            $(document).on('click', '.fail-delivery-btn', function(e){
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url');
                Swal.fire({
                    title: 'Отметить как неудачную?',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Да, отметить',
                    showLoaderOnConfirm: true,
                    preConfirm: () => $.post(url, {_token: "{{ csrf_token() }}"})
                }).then((result) => { if(result.isConfirmed){ location.reload(); } });
            });
            @endif
        });
    </script>
@endpush
