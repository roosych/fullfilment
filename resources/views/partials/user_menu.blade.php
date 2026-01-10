<div class="app-navbar-item ms-3" id="kt_header_user_menu_toggle">
    <div class="cursor-pointer symbol symbol-circle symbol-40px me-6"
         data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
         data-kt-menu-attach="parent"
         data-kt-menu-placement="bottom-end"
    >
        <div class="symbol-label fs-3 bg-light-primary text-primary">
            {{ mb_substr(auth()->user()->name, 0, 1) }}
        </div>
    </div>

    @if(Auth::user()->hasRole('admin'))
        <a href="#" class="btn btn-flex btn-center btn-warning align-self-center p-3 px-lg-4 h-35px me-3"
           data-bs-toggle="modal"
           data-bs-target="#stockReceiveModal">
            <i class="ki-duotone ki-cube-2 fs-2 p-0 m-0">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span></i>
            <span class="ms-2 d-none d-lg-block">
                Прием товара
            </span>
        </a>

        @include('admin.stock.modals.stock_receive_modal')

        <a href="{{route('dashboard.order.create')}}" class="btn btn-flex btn-center btn-success align-self-center p-3 px-lg-4 h-35px"
    {{--       data-bs-toggle="modal"--}}
    {{--       data-bs-target="#kt_modal_invite_friends"--}}
        >
            <i class="ki-duotone ki-plus-square fs-2 p-0 m-0">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            <span class="ms-2 d-none d-lg-block">
                Заказ
            </span>
        </a>
    @elseif(Auth::user()->hasRole('merchant'))
        <a href="{{route('cabinet.orders.create')}}" class="btn btn-flex btn-center btn-success align-self-center p-3 px-lg-4 h-35px me-3">
            <i class="ki-duotone ki-plus-square fs-2 p-0 m-0">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            <span class="ms-2 d-none d-lg-block">
                Доставка
            </span>
        </a>
        <a href="#" class="btn btn-flex btn-center btn-danger align-self-center p-3 px-lg-4 h-35px">
            <i class="ki-duotone ki-arrows-circle fs-2 p-0 m-0">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
            <span class="ms-2 d-none d-lg-block">
                Возврат
            </span>
        </a>
    @endif

    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">

        <div class="menu-item px-3">
            <div class="menu-content d-flex align-items-center px-3">
                <div class="symbol symbol-50px me-5">
                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <div class="fw-bold d-flex align-items-center fs-5">
                        {{auth()->user()->name}}
                    </div>
                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                        {{auth()->user()->email}}
                    </a>
                </div>
            </div>
        </div>

        <div class="separator my-2"></div>

        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
            <a href="#" class="menu-link px-5">
                  <span class="menu-title position-relative">
                  Тема
                  <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                  <i class="ki-duotone ki-night-day theme-light-show fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>                        <i class="ki-duotone ki-moon theme-dark-show fs-2"><span class="path1"></span><span class="path2"></span></i>                    </span>
                  </span>
            </a>

            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">

                <div class="menu-item px-3 my-0">
                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                          <span class="menu-icon" data-kt-element="icon">
                          <i class="ki-duotone ki-night-day fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>            </span>
                            <span class="menu-title">
                          Светлая
                          </span>
                    </a>
                </div>

                <div class="menu-item px-3 my-0">
                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                      <span class="menu-icon" data-kt-element="icon">
                          <i class="ki-duotone ki-moon fs-2">
                              <span class="path1"></span>
                              <span class="path2"></span>
                          </i>
                      </span>
                        <span class="menu-title">
                            Темная
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
            <a href="{{route('lang.switch', 'ru')}}" class="menu-link px-5">
                  <span class="menu-title position-relative">
                      Язык
                      <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                          @if(app()->getLocale() === 'ru')
                              Русский
                          @else
                              Azərbaycanca
                          @endif
                          <img class="w-15px h-15px rounded-1 ms-2" src="{{asset('assets/img/' . (app()->getLocale() === 'ru' ? 'ru' : 'az') . '.svg')}}" />
                      </span>
                  </span>
            </a>
            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                <div class="menu-item px-3">
                    <a href="{{route('lang.switch', 'ru')}}" class="menu-link d-flex px-5 {{ app()->getLocale() === 'ru' ? 'active' : '' }}">
                        <span class="symbol symbol-20px me-4">
                            <img class="rounded-1" src="{{asset('assets/img/ru.svg')}}" alt=""/>
                        </span>
                        Русский
                    </a>
                </div>

                <div class="menu-item px-3">
                    <a href="{{route('lang.switch', 'az')}}" class="menu-link d-flex px-5 {{ app()->getLocale() === 'az' ? 'active' : '' }}">
                        <span class="symbol symbol-20px me-4">
                            <img class="rounded-1" src="{{asset('assets/img/az.svg')}}" alt=""/>
                        </span>
                        Azərbaycanca
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item px-5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" href="#" class="btn btn-sm w-100 menu-link px-5 fs-6">
                    Выход
                </button>
            </form>
        </div>

    </div>
</div>
