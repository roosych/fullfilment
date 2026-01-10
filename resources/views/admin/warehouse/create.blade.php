@extends('layouts.dashboard')

@section('title', 'Добавить склад')

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
            <a href="{{route('dashboard.warehouses.index')}}" class="text-muted text-hover-primary">
                Warehouses
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Добавить склад
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
                <h3 class="fw-bold mb-1">Новый склад</h3>
                <div class="fs-6 text-gray-500">Заполните данные ниже, чтобы создать новый склад</div>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('dashboard.warehouses.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row mb-6">
                    <div class="col-md-12 mb-4">
                        <label class="form-label required">Название</label>
                        <input type="text" name="name" class="form-control form-control-solid"
                               value="{{ old('name') }}"
                               placeholder="Введите название склада" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Адрес</label>
                        <input type="text" name="address" class="form-control form-control-solid"
                               value="{{ old('address') }}"
                               placeholder="Введите адрес">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="form-label">Примечания</label>
                    <textarea name="notes" class="form-control form-control-solid" rows="3"
                              placeholder="Дополнительные детали...">{{ old('notes') }}</textarea>
                </div>

                <div class="form-check form-switch mb-8">
                    <input class="form-check-input" type="checkbox" name="active" id="activeSwitch" {{ old('active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeSwitch">Активен</label>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dashboard.warehouses.index') }}" class="btn btn-light me-3">Отмена</a>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
@endsection
