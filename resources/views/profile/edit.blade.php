@extends('layouts.dashboard')

@section('title', 'Профиль')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">Профиль</li>
    </ul>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success d-flex align-items-center p-5 mb-5">
            <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-success">Профиль обновлен</h4>
                <span>Данные профиля успешно сохранены.</span>
            </div>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success d-flex align-items-center p-5 mb-5">
            <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-success">Пароль изменен</h4>
                <span>Пароль успешно обновлен.</span>
            </div>
        </div>
    @endif

    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Информация о профиле</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="form">
                        @csrf
                        @method('patch')

                        <div class="mb-6">
                            <label for="name" class="form-label required">Имя</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="email" class="form-label required">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7 mt-2">
                                На этот email будут приходить уведомления
                            </div>

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="form-text text-warning mt-2">
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                        @csrf
                                        Ваш email не подтвержден.
                                        <button type="submit" class="btn btn-link p-0 text-warning text-decoration-underline">
                                            Отправить подтверждение
                                        </button>
                                    </form>
                                </div>

                                @if (session('status') === 'verification-link-sent')
                                    <div class="form-text text-success mt-2">
                                        Новая ссылка подтверждения отправлена на ваш email.
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" id="profileSubmitBtn" class="btn btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Смена пароля</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form id="passwordForm" method="POST" action="{{ route('password.update') }}" class="form">
                        @csrf
                        @method('put')

                        <div class="mb-6">
                            <label for="current_password" class="form-label required">Текущий пароль</label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}"
                                   autocomplete="current-password"
                                   required>
                            @if($errors->updatePassword->has('current_password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                            @endif
                        </div>

                        <div class="mb-6">
                            <label for="password" class="form-label required">Новый пароль</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}"
                                   autocomplete="new-password"
                                   required>
                            @if($errors->updatePassword->has('password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                            @endif
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="form-label required">Подтверждение пароля</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}"
                                   autocomplete="new-password"
                                   required>
                            @if($errors->updatePassword->has('password_confirmation'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" id="passwordSubmitBtn" class="btn btn-primary">Изменить пароль</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_js')
    <script>
        $(document).ready(function() {
            // Обработчик отправки формы профиля
            $('#profileForm').on('submit', function(e) {
                const $submitBtn = $('#profileSubmitBtn');
                
                // Блокируем кнопку и меняем содержимое на текст с крутилкой
                $submitBtn.prop('disabled', true);
                $submitBtn.html('Сохранение... <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>');
            });

            // Обработчик отправки формы смены пароля
            $('#passwordForm').on('submit', function(e) {
                const $submitBtn = $('#passwordSubmitBtn');
                
                // Блокируем кнопку и меняем содержимое на текст с крутилкой
                $submitBtn.prop('disabled', true);
                $submitBtn.html('Изменение... <span class="spinner-border spinner-border-sm align-middle ms-2" role="status" aria-hidden="true"></span>');
            });
        });
    </script>
@endpush
