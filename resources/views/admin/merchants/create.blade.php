@extends('layouts.dashboard')

@section('title', 'Добавить мерчанта')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">
                Панель управления
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.merchants.index')}}" class="text-muted text-hover-primary">
                Мерчанты
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Новый мерчант
        </li>
    </ul>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="card">
        <div class="card-body py-4">
            <form id="merchantForm" action="{{ route('dashboard.merchants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- User Info -->
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email (необязательно)</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                </div>

                <!-- Merchant Info -->
                <div class="mb-3">
                    <label for="company" class="form-label">Название компании</label>
                    <input type="text" name="company" id="company" class="form-control"
                           value="{{ old('company') }}" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Адрес</label>
                    <input type="text" name="address" id="address" class="form-control"
                           value="{{ old('address') }}">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Контактный телефон</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone') }}">
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Аватар</label>
                    <input type="file" name="avatar" id="avatar" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="id_card" class="form-label">ID карта</label>
                    <input type="file" name="id_card" id="id_card" class="form-control" accept="image/*,application/pdf">
                    <small class="form-text text-muted">Загрузите файл ID карты (JPG, PNG или PDF, макс. 10MB)</small>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Заметки</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Баланс (в манатах)</label>
                    <input type="number" name="balance" id="balance" class="form-control"
                           value="{{ old('balance', 0) }}" step="0.01" min="0">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard.merchants.index') }}" id="cancelBtn" class="btn btn-light">Отмена</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="indicator-label">
                            Создать мерчанта
                        </span>
                        <span class="indicator-progress d-none">
                            Создается...
                            <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('vendor_css')

@endpush

@push('vendor_js')

@endpush

@push('custom_js')
    <script>
        $(document).ready(function() {
            // Обработчик отправки формы с индикатором загрузки
            $('#merchantForm').on('submit', function(e) {
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
