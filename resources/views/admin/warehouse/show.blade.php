@extends('layouts.dashboard')

@section('title', 'Редактировать склад')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">
                Панель управления
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.warehouses.index') }}" class="text-muted text-hover-primary">
                Warehouses
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Редактировать склад
        </li>
    </ul>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    @if ($errors->any())
        <x-alert type="danger" :message="implode('<br>', $errors->all())"/>
    @endif

    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column pt-3">
                <h3 class="fw-bold mb-1">Редактировать склад</h3>
                <div class="fs-6 text-gray-500">Обновите данные ниже, чтобы изменить этот склад</div>
            </div>
        </div>

        <div class="card-body">
            <form id="warehouseEditForm" action="{{ route('dashboard.warehouses.update', $warehouse) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-6">
                    <div class="col-md-12 mb-4">
                        <label class="form-label required">Название</label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-solid"
                               value="{{ old('name', $warehouse->name) }}"
                               placeholder="Введите название склада"
                               required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Адрес</label>
                        <input type="text"
                               name="address"
                               class="form-control form-control-solid"
                               value="{{ old('address', $warehouse->address) }}"
                               placeholder="Введите адрес">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="form-label">Примечания</label>
                    <textarea name="notes"
                              class="form-control form-control-solid"
                              rows="3"
                              placeholder="Дополнительные детали...">{{ old('notes', $warehouse->notes) }}</textarea>
                </div>

                <div class="form-check form-switch mb-8">
                    <input class="form-check-input"
                           type="checkbox"
                           name="active"
                           id="activeSwitch"
                        {{ old('active', $warehouse->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeSwitch">Активен</label>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dashboard.warehouses.index') }}" id="cancelBtn" class="btn btn-light me-3">Отмена</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="indicator-label">
                            Обновить
                        </span>
                        <span class="indicator-progress d-none">
                            Обновление...
                            <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom_js')
    <script>
        $(document).ready(function() {
            // Обработчик отправки формы с индикатором загрузки
            $('#warehouseEditForm').on('submit', function(e) {
                const $submitBtn = $('#submitBtn');
                const $cancelBtn = $('#cancelBtn');
                
                // Блокируем кнопку отправки и меняем содержимое на текст с крутилкой
                $submitBtn.prop('disabled', true);
                $submitBtn.html('Обновление... <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>');
                
                // Блокируем кнопку отмены
                $cancelBtn.addClass('disabled').css('pointer-events', 'none').css('opacity', '0.6');
            });
        });
    </script>
@endpush
