<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>
        @yield('title')
    </title>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content="" />
    <link rel="canonical" href=""/>
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <link href="{{asset('assets/css/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/waitMe.min.css')}}" rel="stylesheet" type="text/css"/>
    @stack('vendor_css')
</head>

<body id="kt_body" data-kt-app-header-stacked="true" data-kt-app-header-primary-enabled="true" data-kt-app-header-secondary-enabled="true" data-kt-app-toolbar-enabled="true"  class="app-default" >

<script>
    let defaultThemeMode = "light";
    let themeMode;
    if ( document.documentElement ) {
        if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if ( localStorage.getItem("data-bs-theme") !== null ) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
</script>
<div class="preloader">
    <div class="spinner-border text-primary" role="status"></div>
</div>
<style>
    .preloader{position:fixed;left:0;top:0;width:100%;height:100%;background-color:#fff;display:flex;justify-content:center;align-items:center;z-index:9999}
</style>
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <div class="app-page  flex-column" id="kt_app_page">
        @include('partials.header')
    </div>

    <div class="app-wrapper flex-column flex-row-fluid " id="kt_app_wrapper">
        <div class="app-container  container-xxl d-flex flex-row flex-column-fluid">
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

                <div id="kt_app_toolbar" class="app-toolbar pt-lg-9 pt-6">
                    <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex flex-stack flex-wrap">
                        <div class="d-flex flex-stack flex-wrap gap-4 w-100">

                            <div class="page-title d-flex flex-column gap-3 me-3">
                                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bolder fs-2x my-0">
                                    @yield('title')
                                </h1>

                                @yield('breadcrumbs')
                            </div>

                           @yield('actions')

                        </div>
                    </div>
                </div>
                <div id="kt_app_content" class="app-content  pb-0 ">
                    @yield('content')
                </div>

                @include('partials.footer')
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<script src="{{asset('assets/js/widgets.bundle.js')}}"></script>
<script src="{{asset('assets/js/waitMe.min.js')}}"></script>
<script>
    $(window).on('load', function() {
        $('.preloader').fadeOut('slow');
    });
</script>
@stack('vendor_js')
@stack('custom_js')
</body>
</html>
