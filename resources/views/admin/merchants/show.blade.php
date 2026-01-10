@extends('layouts.dashboard')

@section('title', 'Мерчанты - ' . $merchant->user->name)

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">Панель управления</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.merchants.index')}}" class="text-muted text-hover-primary">Мерчанты</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            {{$merchant->user->name}}
        </li>
    </ul>
@endsection

@section('actions')
    <div class="d-flex align-items-center gap-3 gap-lg-5">
        <a href="{{ route('dashboard.merchants.edit', $merchant) }}" class="btn btn-sm btn-flex btn-center btn-primary px-4">
            <i class="ki-duotone ki-pencil fs-2 me-2">
                <span class="path1"></span><span class="path2"></span>
            </i>
            Редактировать
        </a>
        <a href="#" class="btn btn-sm btn-flex btn-center btn-dark px-4" data-bs-toggle="modal" data-bs-target="#stockReceiveModal">
            Прием товара
        </a>
        <a href="#" class="btn btn-flex btn-sm btn-color-gray-700 bg-body fw-bold px-4">
            Отчеты
        </a>
    </div>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="d-flex flex-column flex-xl-row">
        <!-- LEFT SIDEBAR -->
        <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body pt-15">

                    <!-- Avatar -->
                    <div class="d-flex flex-center flex-column mb-5">
                        <div class="symbol symbol-150px symbol-circle mb-7">
                            @if($merchant->avatar)
                                <img src="{{$merchant->avatar}}" alt="{{$merchant->user->name}}">
                            @else
                                <div class="symbol-label fs-3 bg-light-dark text-dark">
                                    {{ mb_substr($merchant->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <p class="fs-3 text-gray-800 fw-bold me-1 mb-0">{{ $merchant->user->name }}</p>

                            @if($merchant->is_verified)
                                <i class="ki-duotone ki-verify fs-1 text-primary">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            @endif
                        </div>

                        <p class="fs-5 fw-semibold text-muted mb-6">{{$merchant->phone}}</p>
                    </div>

                    <!-- Details -->
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bold">Details</div>
                    </div>
                    <div class="separator separator-dashed my-3"></div>

                    <div class="pb-5 fs-6">
                        <div class="fw-bold mt-5">Account ID</div>
                        <div class="text-gray-600">{{$merchant->user->id}}</div>

                        <div class="fw-bold mt-5">Email</div>
                        <div class="text-gray-600">
                            <a href="#" class="text-gray-600 text-hover-primary">{{$merchant->user->email}}</a>
                        </div>

                        <div class="fw-bold mt-5">Company</div>
                        <div class="text-gray-600">{{$merchant->company}}</div>

                        <div class="fw-bold mt-5">Address</div>
                        <div class="text-gray-600">{{$merchant->address}}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-lg-row-fluid ms-lg-15">

            <!-- Tabs -->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#merchant_overview">Обзор</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#merchant_transactions">Транзакции</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#merchant_orders">Заказы</a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content" id="myTabContent">

                {{-- =============================
                    ВКЛАДКА 1 — ОБЗОР
                ============================= --}}
                <div class="tab-pane fade show active" id="merchant_overview" role="tabpanel">

                    <div class="row row-cols-1 row-cols-md-2 mb-6 mb-xl-9">

                        <!-- Balance -->
                        <div class="col">
                            <div class="card pt-4 h-md-100 mb-6 mb-md-0">
                                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                    <h2 class="fw-bold mb-0">Баланс</h2>

                                    <button type="button" class="btn btn-sm btn-light-success"
                                            data-bs-toggle="modal" data-bs-target="#top_up_modal"
                                            data-merchant-id="{{ $merchant->id }}">
                                        <i class="ki-duotone ki-plus-square fs-2 p-0 me-2">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                        Пополнить
                                    </button>
                                </div>

                                <div class="card-body pt-0">
                                    <div class="fw-bold fs-2">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-wallet text-info fs-2x me-2">
                                                <span class="path1"></span><span class="path2"></span>
                                                <span class="path3"></span><span class="path4"></span>
                                            </i>

                                            <div id="merchant_balance_{{$merchant->id}}">
                                                {{ money($merchant->balance) }}
                                            </div>
                                        </div>

                                        <div class="fs-7 fw-normal text-muted mt-1">
                                            Current balance of the merchant account.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock overview box -->
                        <div class="col">
                            <a href="{{route('dashboard.merchants.stock', $merchant)}}" class="card bg-info hoverable h-md-100">
                                <div class="card-body">
                                    <i class="ki-duotone ki-delivery-2 text-white fs-3x ms-n1">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        <span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span>
                                    </i>

                                    <div class="text-white fw-bold fs-2 mt-5">Обзор склада</div>
                                    <div class="fw-semibold text-white">Доступный товар на всех складах.</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Last deliveries -->
                    <div class="row">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-900">Last Deliveries</span>
                                </h3>

                                <div class="card-toolbar">
                                    <div class="d-flex flex-stack flex-wrap gap-4">
                                        <a href="#" class="btn btn-light btn-sm">All Deliveries</a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table align-middle table-row-dashed fs-6 gy-3">
                                    <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">ID</th>
                                        <th class="text-end pe-3 min-w-100px">Order</th>
                                        <th class="text-end pe-3 min-w-150px">Rate</th>
                                        <th class="text-end pe-3 min-w-100px">Zone</th>
                                        <th class="text-end pe-3 min-w-100px">Weight</th>
                                        <th class="text-end pe-3 min-w-100px">Status</th>
                                        <th class="text-end pe-0 min-w-75px">Price</th>
                                    </tr>
                                    </thead>

                                    <tbody class="fw-bold text-gray-600">
                                    @forelse($lastDeliveries as $delivery)
                                        <tr>
                                            <td>
                                                <a href="#" class="text-gray-900 text-hover-primary">#{{$delivery->id}}</a>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" class="text-gray-900 text-hover-primary">#{{$delivery->order->id}}</a>
                                            </td>
                                            <td class="text-end">{{$delivery->rate->name}}</td>
                                            <td class="text-end">{{$delivery->zone->name}}</td>
                                            <td class="text-end">{{$delivery->weight}}</td>
                                            <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-{{$delivery->status->colorClass()}}">
                                                        {{$delivery->status->label()}}
                                                    </span>
                                            </td>
                                            <td class="text-end">{{money($delivery->price)}}</td>
                                        </tr>
                                    @empty
                                        empty
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div> <!-- END TAB 1 -->

                {{-- =============================
                    ВКЛАДКА 2 — ТРАНЗАКЦИИ
                ============================= --}}
                <div class="tab-pane fade" id="merchant_transactions" role="tabpanel">

                    <!-- Balance Transactions -->
                    <div class="card pt-4 mb-6 mb-xl-9">
                        <div class="card-header border-0">
                            <div class="card-title">
                                <h2>Транзакции баланса</h2>
                            </div>
                        </div>

                        <div class="card-body pt-0 pb-5">
                            <table class="table align-middle table-row-dashed gy-5">
                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                <tr class="text-start text-muted text-uppercase gs-0">
                                    <th class="min-w-100px">uuid</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th class="min-w-100px">Date</th>
                                </tr>
                                </thead>

                                <tbody class="fs-6 fw-semibold text-gray-600">
                                @foreach($merchant->transactions as $transaction)
                                    <tr>
                                        <td>{{$transaction->id}}</td>
                                        <td>
                                                <span class="badge badge-light-{{ $transaction->type->colorClass() }} fw-bold px-4 py-3">
                                                    {{$transaction->type->label()}}
                                                </span>
                                        </td>
                                        <td>{{money($transaction->amount)}}</td>
                                        <td>{{\Carbon\Carbon::parse($transaction->created_at)->isoFormat('DD MMM Y, HH:mm')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stock intake history -->
                    <div class="card pt-4 mb-6 mb-xl-9">
                        <div class="card-header border-0">
                            <div class="card-title">
                                <h2>Stock Intake History</h2>
                            </div>
                        </div>

                        <div class="card-body pt-0 pb-5">
                            <table class="table align-middle table-row-dashed gy-5">
                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                <tr class="text-start text-muted text-uppercase gs-0">
                                    <th class="min-w-100px">Code</th>
                                    <th>Warehouse</th>
                                    <th>Product Count</th>
                                    <th>Total Quantity</th>
                                    <th class="min-w-100px">Received at</th>
                                </tr>
                                </thead>

                                <tbody class="fs-6 fw-semibold text-gray-600">
                                @foreach($merchant->stock_batches as $batch)
                                    <tr>
                                        <td>
                                            <a href="{{route('dashboard.batch.receipt', $batch)}}" class="text-gray-800 text-hover-primary" target="_blank">
                                                {{$batch->batch_code}}
                                            </a>
                                        </td>
                                        <td>{{$batch->warehouse->name}}</td>
                                        <td>{{$batch->entries->count()}}</td>
                                        <td>{{$batch->entries->sum('quantity')}}</td>
                                        <td>{{\Carbon\Carbon::parse($batch->received_at)->isoFormat('DD MMM Y, HH:mm')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <!-- END TAB 2 -->

                {{-- =============================
                    TAB 3 — ORDERS
                ============================= --}}
                <div class="tab-pane fade" id="merchant_orders" role="tabpanel">
                    tab 3
                </div>

            </div> <!-- END tab-content -->

        </div> <!-- END MAIN COLUMN -->
    </div>

    @include('admin.merchants.modals.top-up')
@endsection

@push('vendor_css')
@endpush

@push('vendor_js')
    <script src="{{asset('assets/js/plugins/datatables.bundle.js')}}"></script>
@endpush

@push('custom_js')
@endpush
