@extends('layouts.dashboard')

@section('title', 'Stock In - ' . $merchant->user->name)

@section('content')
    <div class="mb-7">
        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
            <i class="ki-outline ki-box fs-2tx text-primary me-4"></i>
            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                <div class="mb-3 mb-md-0 fw-semibold">
                    <h4 class="text-gray-900 fw-bold">New Stock Entry</h4>
                    <div class="fs-6 text-gray-700 pe-7">
                        Adding products to
                        <span class="fw-bold text-gray-800 fs-5">{{$warehouse->name}}</span>
                        for
                        <a href="{{route('dashboard.merchants.show', $merchant)}}" target="_blank"
                           class="fw-bold text-gray-800 text-hover-primary fs-5">
                            {{$merchant->user->name}}
                        </a>
                    </div>
                </div>

                <button type="button" class="btn btn-primary px-6 align-self-center text-nowrap"
                        data-bs-toggle="modal"
                        data-bs-target="#stockReceiveModal">
                    Change
                </button>
            </div>
        </div>
    </div>
    <form action="{{ route('dashboard.stock.preview', [$merchant, $warehouse]) }}" method="POST" id="stock-form">
        @csrf
        <div class="card card-flush mb-5 mb-lg-10">
            <div class="card-body">
                <div id="kt_docs_repeater_basic">
                    <div class="form-group">
                        <div data-repeater-list="products">
                            @if(!empty($oldData['products']))
                                @foreach($oldData['products'] as $oldProduct)
                                    <div data-repeater-item class="mb-4">
                                        <div class="d-flex flex-wrap align-items-end gap-3">
                                            <!-- Product Select / New Product Input -->
                                            <div class="flex-grow-1 min-w-200">
                                                <label class="form-label">Product:</label>
                                                <select name="product_id" class="form-select product-select" data-placeholder="Select a product" required>
                                                    <option value="">-- Select a product --</option>
                                                    <option value="new" @if(is_null($oldProduct['product_id'])) selected @endif>+ Add new product</option>
                                                    @foreach($merchant->products as $product)
                                                        <option value="{{ $product->id }}" @if($oldProduct['product_id'] == $product->id) selected @endif>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>

                                                <input type="text" name="new_product_name" class="form-control form-control-solid new-product-field @if(!is_null($oldProduct['product_id'])) d-none @endif mt-2"
                                                       placeholder="Enter new product name" value="{{ $oldProduct['new_product_name'] ?? '' }}" />
                                            </div>

                                            <!-- Quantity -->
                                            <div class="flex-shrink-0" style="width: 140px;">
                                                <label class="form-label">Quantity:</label>
                                                <input type="number" name="quantity" class="form-control form-control-solid" min="1" value="{{ $oldProduct['quantity'] ?? 1 }}" required />
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="flex-shrink-0">
                                                <button type="button" data-repeater-delete class="btn btn-light-danger mt-3 mt-md-0">
                                                    <i class="ki-duotone ki-trash fs-5"></i>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div data-repeater-item class="mb-4">
                                    <div class="d-flex flex-wrap align-items-end gap-3">
                                        <!-- Product Select / New Product Input -->
                                        <div class="flex-grow-1 min-w-200">
                                            <label class="form-label">Product:</label>
                                            <select name="product_id" class="form-select product-select" data-placeholder="Select a product" required>
                                                <option value="">-- Select a product --</option>
                                                <option value="new">+ Add new product</option>
                                                @foreach($merchant->products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>

                                            <input type="text" name="new_product_name" class="form-control form-control-solid new-product-field d-none mt-2"
                                                   placeholder="Enter new product name" />
                                        </div>

                                        <!-- Quantity -->
                                        <div class="flex-shrink-0" style="width: 140px;">
                                            <label class="form-label">Quantity:</label>
                                            <input type="number" name="quantity" class="form-control form-control-solid" min="1" value="1" required />
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="flex-shrink-0">
                                            <button type="button" data-repeater-delete class="btn btn-light-danger mt-3 mt-md-0">
                                                <i class="ki-duotone ki-trash fs-5"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group mt-8">
                            <button type="button" data-repeater-create class="btn btn-light-primary">
                                <i class="ki-duotone ki-plus fs-3"></i> Add product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-5">
            <button type="submit" class="btn btn-primary">Preview</button>
        </div>
    </form>
@endsection

@push('custom_js')
    <script src="{{ asset('assets/js/plugins/formrepeater.bundle.js') }}"></script>
    <script>
        $(document).ready(function () {

            function initSelect2($container) {
                $container.find('.product-select').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: $(this).data('placeholder') || 'Select a product',
                            minimumResultsForSearch: 0,
                            width: '100%'
                        });

                        // Обработчик выбора для Select2
                        $(this).on('select2:select', function () {
                            const $row = $(this).closest('[data-repeater-item]');
                            toggleProductInput($row);
                        });
                    }
                });
            }

            // Показ / скрытие input / select
            function toggleProductInput($row) {
                const $select = $row.find('.product-select');
                const $input = $row.find('.new-product-field');

                if ($select.val() === 'new' || $input.val().trim() !== '') {
                    $select.next('.select2-container').hide();
                    $select.prop('required', false);
                    $input.removeClass('d-none').prop('required', true);
                } else {
                    $select.next('.select2-container').show();
                    $select.prop('required', true);
                    $input.addClass('d-none').prop('required', false).val('');
                }
            }

            // Инициализация repeater
            $('#kt_docs_repeater_basic').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                    initSelect2($(this));
                    toggleProductInput($(this));
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });

            // Инициализация Select2 для существующих рядов
            initSelect2($('#kt_docs_repeater_basic'));

            // Применяем toggle для всех рядов при загрузке страницы
            $('#kt_docs_repeater_basic [data-repeater-item]').each(function () {
                toggleProductInput($(this));
            });
        });

    </script>
@endpush
