@extends('layouts.dashboard')

@section('title', 'Редактировать мерчанта - ' . $merchant->user->name)

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">
                Main
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
            <a href="{{route('dashboard.merchants.show', $merchant)}}" class="text-muted text-hover-primary">
                {{ $merchant->user->name }}
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Редактировать
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

    <div class="card">
        <div class="card-body py-4">
            <form action="{{ route('dashboard.merchants.update', $merchant) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- User Info -->
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $merchant->user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email (необязательно)</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $merchant->user->email) }}">
                </div>

                <!-- Merchant Info -->
                <div class="mb-3">
                    <label for="company" class="form-label">Название компании</label>
                    <input type="text" name="company" id="company" class="form-control"
                           value="{{ old('company', $merchant->company) }}" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Адрес</label>
                    <input type="text" name="address" id="address" class="form-control"
                           value="{{ old('address', $merchant->address) }}">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Контактный телефон</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $merchant->phone) }}">
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Аватар</label>
                    <input type="file" name="avatar" id="avatar" class="form-control">
                    @if($merchant->avatar)
                        <small class="form-text text-muted">Текущий аватар: {{ $merchant->avatar }}</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="id_card" class="form-label">ID карта</label>
                    <input type="file" name="id_card" id="id_card" class="form-control" accept="image/*,application/pdf">
                    @if($merchant->id_card)
                        @php
                            $filePath = 'merchants/id_cards/' . $merchant->id_card;
                            $fileUrl = asset('storage/' . $filePath);
                            $extension = strtolower(pathinfo($merchant->id_card, PATHINFO_EXTENSION));
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                        @endphp
                        @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath))
                            <div class="mt-3">
                                <small class="form-text text-muted d-block mb-2">Текущий файл: {{ $merchant->id_card }}</small>
                                @if($isImage)
                                    <div class="border rounded p-2 d-inline-block">
                                        <a href="{{ $fileUrl }}" target="_blank" class="d-inline-block">
                                            <img src="{{ $fileUrl }}" alt="ID Card" class="img-fluid" style="max-height: 300px; max-width: 400px; cursor: pointer;">
                                        </a>
                                    </div>
                                @else
                                    <div class="border rounded p-3 d-inline-block">
                                        <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none">
                                            <i class="ki-duotone ki-file fs-2x text-primary">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                            <div class="mt-2">PDF файл - нажмите для просмотра</div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                    <small class="form-text text-muted">Загрузите новый файл ID карты (JPG, PNG или PDF, макс. 10MB). Если файл не выбран, текущий файл останется без изменений.</small>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Заметки</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $merchant->notes) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard.merchants.show', $merchant) }}" class="btn btn-light">Отмена</a>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
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

@endpush

