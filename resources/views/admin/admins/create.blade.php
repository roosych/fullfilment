@extends('layouts.dashboard')

@section('title', 'Добавить администратора')

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
            <a href="{{route('dashboard.admins.index')}}" class="text-muted text-hover-primary">
                Администраторы
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            New Administrator
        </li>
    </ul>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="card">
        <div class="card-body py-4">
            <form action="{{ route('dashboard.admins.store') }}" method="POST">
                @csrf

                <!-- User Info -->
                <div class="mb-3">
                    <label for="name" class="form-label required">Имя</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label required">Телефон</label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone') }}" 
                           placeholder="(050) 555 55 55"
                           autocomplete="off"
                           required>
                    <small class="form-text text-muted">Введите номер в формате: 0505555555</small>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           minlength="8">
                    <small class="form-text text-muted">Если оставить пустым, будет сгенерирован автоматически</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           minlength="8">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('dashboard.admins.index') }}" class="btn btn-light">Отмена</a>
                    <button type="submit" class="btn btn-primary">Создать администратора</button>
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
    <script src="{{asset('assets/js/plugins/inputmask.js')}}"></script>
    <script>
        $(document).ready(function() {
            Inputmask({
                "mask" : "(999) 999 99 99",
                "removeMaskOnSubmit": false
            }).mask("#phone");
        });
    </script>
@endpush

