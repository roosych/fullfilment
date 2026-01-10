<div class="app-header-secondary">
    <div class="app-container  container-xxl d-flex align-items-stretch " id="kt_app_header_secondary_container">

        <div
            class="app-header-menu app-header-mobile-drawer align-items-stretch flex-grow-1"
            data-kt-drawer="true"
            data-kt-drawer-name="app-header-menu"
            data-kt-drawer-activate="{default: true, lg: false}"
            data-kt-drawer-overlay="true"
            data-kt-drawer-width="250px"
            data-kt-drawer-direction="start"
            data-kt-drawer-toggle="#kt_app_header_menu_toggle"
            data-kt-swapper="true"
            data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
            data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}"
        >

            <div
                class=" menu
                              menu-rounded
                              menu-active-bg
                              menu-state-primary
                              menu-column
                              menu-lg-row
                              menu-title-gray-700
                              menu-icon-gray-500
                              menu-arrow-gray-500
                              menu-bullet-gray-500
                              my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0
                              "
                id="kt_app_header_menu"
                data-kt-menu="true"
            >

                <div class="menu-item {{active_link(['cabinet.dashboard'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.dashboard')}}" class="menu-title">
                            Главная
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['cabinet.orders.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.orders.index')}}" class="menu-title">
                            Доставки
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['cabinet.products.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.products.index')}}" class="menu-title">
                            Товары
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['cabinet.stock.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.stock.index')}}" class="menu-title">
                            Пополнения склада
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['cabinet.transactions.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.transactions.index')}}" class="menu-title">
                            Транзакции
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['cabinet.rates.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('cabinet.rates.index')}}" class="menu-title">
                            Тарифы и цены
                        </a>
                    </span>
                </div>

            </div>
        </div>

        {{--<div class="d-flex align-items-center w-100 w-lg-225px pt-5 pt-lg-0">
            <div class="header-search d-flex align-items-center w-100 w-lg-225px">
                <input type="text" class="search-input form-control" name="search" value="" placeholder="Поиск..."/>
            </div>
        </div>--}}
    </div>
</div>
