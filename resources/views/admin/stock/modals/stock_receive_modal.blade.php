@php
    $merchants = \App\Models\Merchant::with('user')
            ->whereHas('user', function ($query) {
                $query->where('active', true);
            })
            ->latest()
            ->get();
    $warehouses = \App\Models\Warehouse::query()->where('active', true)->get();
@endphp

<div class="modal fade" id="stockReceiveModal" tabindex="-1" aria-labelledby="stockReceiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pb-0">

                <div class="mb-10 text-center">
                    <h1 class="mb-3">Добавить товар на склад</h1>
                    <div class="text-muted fw-semibold fs-5">
                        Выберите мерчанта и склад, куда поступят новые товары.
                    </div>
                </div>
                <div class="mb-8">
                    <label for="merchant" class="form-label">
                        Мерчант
                    </label>
                    <select id="merchant" class="form-select"
                            data-control="select2"
                            data-placeholder="Выберите мерчанта..."
                    >
                        <option value=""></option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->uuid }}" {{ request()->route('merchant.uuid') === $merchant->uuid ? 'selected' : '' }}>
                                {{ $merchant->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fs-6 fw-semibold mb-3">
                        Склад назначения
                    </label>
                    <div data-kt-buttons="true">
                        @foreach($warehouses as $warehouse)
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5
                            {{$warehouse->is_primary ? 'active' : ''}}
                            ">
                            <div class="d-flex align-items-center me-2">
                                <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="warehouse" {{$warehouse->is_primary ? 'checked' : ''}}
                                           value="{{ $warehouse->uuid }}"/>
                                </div>

                                <div class="flex-grow-1">
                                    <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                        {{$warehouse->name}}
                                        @if($warehouse->is_primary)
                                            <span class="badge badge-light-success ms-2 fs-7">По умолчанию</span>
                                        @endif
                                    </h2>
                                    <div class="fw-semibold opacity-50">
                                        {{$warehouse->address}}
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
                <button type="button" id="proceedButton" class="btn btn-primary">Продолжить</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('proceedButton').addEventListener('click', function() {
        const merchantUuid = document.getElementById('merchant').value;
        const warehouseRadio = document.querySelector('input[name="warehouse"]:checked');
        const warehouseUuid = warehouseRadio ? warehouseRadio.value : null;

        if (!merchantUuid || !warehouseUuid) {
            Swal.fire({
                icon: 'warning',
                title: 'Требуется выбор',
                html: 'Пожалуйста, выберите <b>мерчанта</b> и <b>склад</b> для продолжения.',
                confirmButtonText: 'ОК',
                customClass: {
                    popup: 'swal2-border-radius'
                }
            });
            return;
        }

        const url = "{{ route('dashboard.stock.create', ['merchant' => ':merchant', 'warehouse' => ':warehouse']) }}";
        window.location.href = url.replace(':merchant', merchantUuid).replace(':warehouse', warehouseUuid);
    });
</script>
