<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Dostavim.az - Warehouse & Delivery</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="" />
    <meta property="og:site_name" content="" />
    <link rel="canonical" href="" />
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{asset('assets/css/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/plugins/waitMe.min.css')}}" rel="stylesheet" type="text/css" />
</head>
<body id="kt_body" class="app-blank app-blank">

<script>let defaultThemeMode = "light"; let themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>

<div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center"
             style="background-image: url('{{asset('assets/img/header-bg.png')}}')">
            <div class="d-flex flex-column flex-center p-6 p-lg-10 w-100">
                <img class="mb-6" src="{{asset('assets/img/s_logo.png')}}" alt="" width="70"/>
                <h1 class="d-none d-lg-block text-white fs-2qx fw-bold text-center mb-7">
                    Warehouse & Delivery
                </h1>
                <p class="d-none d-lg-block fw-semibold fs-3 text-white">
                    Simple. Fast. Reliable.
                </p>

            </div>
        </div>
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <div class="w-lg-500px p-10">
                    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center mb-8">
                            <h1 class="text-gray-900 fw-bolder">
                                Вход в кабинет
                            </h1>
                        </div>
                        <div class="fv-row mb-8">
                            <input type="text"
                                   id="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="Номер телефона"
                                   name="phone"
                                   autocomplete="off"
                                   required
                                   class="form-control bg-transparent" />
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                @foreach ((array) $errors->get('phone') as $message)
                                    <p class="mb-0">{{ $message }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="fv-row mb-3">
                            <input type="password"
                                   value=""
                                   placeholder="Пароль"
                                   name="password"
                                   autocomplete="off"
                                   required
                                   class="form-control bg-transparent" />
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                @foreach ((array) $errors->get('password') as $message)
                                    <p class="mb-0">{{ $message }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                            <div></div>
                        </div>
                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label">
                                    Войти
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            <div class="d-flex flex-center flex-wrap px-5">
                <div class="d-flex fw-semibold text-primary fs-base">
                    <a href="{{route('lang.switch', 'ru')}}" class="px-5">
                            <span class="symbol symbol-20px me-1">
                                <img class="rounded-1" src="{{asset('assets/img/ru.svg')}}" alt="" />
                            </span>
                        Русский
                    </a>
                    <a href="{{route('lang.switch', 'az')}}" class="px-5">
                            <span class="symbol symbol-20px me-1">
                                <img class="rounded-1" src="{{asset('assets/img/az.svg')}}" alt="" />
                            </span>
                        Azərbaycanca
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<script src="{{asset('assets/js/plugins/inputmask.js')}}"></script>
<script src="{{asset('assets/js/plugins/waitMe.min.js')}}"></script>
<script>
    $(document).ready(function() {
        Inputmask({
            "mask" : "(999) 999 99 99"
        }).mask("#phone");

        $('#kt_sign_in_form').on('submit', function() {
            const $submitButton = $('#kt_sign_in_submit');
            $submitButton.prop('disabled', true);
            $submitButton.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Проверка...');
        });
    });
</script>
</body>
</html>
