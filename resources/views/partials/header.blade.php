<div id="kt_app_header" class="app-header">
    <div class="app-header-primary "
         data-kt-sticky="true" data-kt-sticky-name="app-header-primary-sticky" data-kt-sticky-offset="{default: 'false', lg: '300px'}"
         @auth()
         @if(Auth::user()->hasRole('merchant'))
         style="background-image: url('{{asset('assets/img/merchant-header-bg.png')}}')"
         @else
         style="background-image: url('{{asset('assets/img/header-bg.png')}}')"
         @endif
         @else
         style="background-image: url('{{asset('assets/img/header-bg.png')}}')"
         @endauth
    >
        <div class="app-container  container-xxl d-flex align-items-stretch justify-content-between " id="kt_app_header_primary_container">
            <div class="d-flex flex-grow-1 flex-lg-grow-0">
                <div class="d-flex align-items-center" id="kt_app_header_logo_wrapper">
                    <button class="d-lg-none btn btn-icon btn-color-white btn-active-color-primary ms-n4 me-sm-2" id="kt_app_header_menu_toggle">
                        <i class="ki-duotone ki-abstract-14 fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </button>

                    <a href="{{url('/')}}" class="d-flex align-items-center mb-1 mb-lg-0 pt-lg-1">
                        <img alt="Logo" src="{{asset('assets/img/s_logo.png')}}" class="d-block d-sm-none h-45px"/>
                        <img alt="Logo" src="{{asset('assets/img/s_logo.png')}}" class="d-none d-sm-block h-45px"/>
                    </a>
                </div>
            </div>
            <div class="app-navbar">
                {{--<div class="app-navbar-item ms-1">
                    <div
                        class="btn btn-icon btn-color-white btn-active-color-primary"
                        data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end"
                    >
                        <i class="ki-duotone ki-element-11 fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-250px w-lg-325px" data-kt-menu="true">
                        <div class="d-flex flex-column flex-center bgi-no-repeat rounded-top px-9 py-10"
                             style="background-image:url('{{asset('assets/img/' . (auth()->check() && auth()->user()->hasRole('merchant') ? 'merchant-header-bg.png' : 'header-bg.png'))}}')">
                            <h3 class="text-white fw-semibold mb-3">
                                Quick links
                            </h3>
                        </div>

                        <div class="row g-0">
                            <div class="col-6">
                                <a href="https://mx.metak.az" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom" target="_blank">
                                    <i class="ki-duotone ki-sms fs-3x text-primary mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span></i>
                                    <span class="fs-5 fw-semibold text-gray-800 mb-0">
                                        Почта
                                    </span>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="https://hrportal.metak.az" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom" target="_blank">
                                    <i class="ki-duotone ki-magnifier fs-3x text-primary mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <span class="fs-5 fw-semibold text-gray-800 mb-0">
                                        HR портал
                                    </span>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="https://tickets.metak.az" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom" target="_blank">
                                    <i class="ki-duotone ki-note-2 fs-3x text-primary mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span></i>
                                    <span class="fs-5 fw-semibold text-gray-800 mb-0">
                                        Тикеты
                                    </span>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="https://intranet.metak.az" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom" target="_blank">
                                    <i class="ki-duotone ki-abstract-41 fs-3x text-primary mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <span class="fs-5 fw-semibold text-gray-800 mb-0">
                                        Интранет
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>--}}

                @include('partials.user_menu')

            </div>
        </div>
    </div>

@auth()
        @if(Auth::user()->hasRole('admin'))
            @include('admin.navigation')
        @endif

        @if(Auth::user()->hasRole('merchant'))
            @include('merchant.navigation')
        @endif
@endauth


</div>
