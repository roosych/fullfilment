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

                <div class="menu-item {{active_link(['dashboard.index'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('dashboard.index')}}" class="menu-title">
                            {{ __('Dashboard') }}
                        </a>
                    </span>
                </div>

                <div class="menu-item {{active_link(['dashboard.order.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('dashboard.order.index')}}" class="menu-title">
                            Orders
                        </a>
                    </span>
                </div>

                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start"
                     class="menu-item {{active_link(['dashboards.users.*', 'dashboard.merchants.*', 'dashboard.admins.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <span  class="menu-title">
                            Users
                        </span>
                        <span class="menu-arrow d-lg-none"></span>
                    </span>
                    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                        <div class="menu-item" >
                            <a class="menu-link"  href="{{route('dashboard.admins.index')}}" title="Список администраторов"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Administrators
                                </span>
                            </a>
                        </div>

                        <div class="menu-item" >
                            <a class="menu-link"  href="{{route('dashboard.merchants.index')}}" title="Список мерчантов"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Merchants
                                </span>
                            </a>
                        </div>

                        <div class="menu-item" >
                            <a class="menu-link"  href="" title="Список курьеров"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Couriers
                                </span>
                            </a>
                        </div>

                        <div class="menu-item" >
                            <a class="menu-link"  href="" title="Список операторов"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Operators
                                </span>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="menu-item {{active_link(['dashboard.warehouses.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="{{route('dashboard.warehouses.index')}}" class="menu-title">
                            Warehouses
                        </a>
                    </span>
                </div>

                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start"
                     class="menu-item {{active_link(['dashboard.delivery-zones.*', 'dashboard.delivery-rates.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <span  class="menu-title">
                            Delivery
                        </span>
                        <span class="menu-arrow d-lg-none"></span>
                    </span>
                    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                        <div class="menu-item">
                            <a class="menu-link" href="{{route('dashboard.delivery-rates.index')}}" title="Список зон доставки"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Tariffs
                                </span>
                            </a>
                        </div>

                        <div class="menu-item" >
                            <a class="menu-link"  href="{{route('dashboard.delivery-zones.index')}}" title="Список зон доставки"
                               data-bs-toggle="tooltip"
                               data-bs-trigger="hover"
                               data-bs-dismiss="click"
                               data-bs-placement="right">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title" >
                                    Zones
                                </span>
                            </a>
                        </div>

                    </div>
                </div>


                <div class="menu-item {{active_link(['dashboard.reports.*'])}} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                    <span class="menu-link">
                        <a href="" class="menu-title">
                            {{ __('Reports') }}
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
