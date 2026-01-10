@extends('layouts.dashboard')
@section('title', 'Предпросмотр приема товара')
@section('content')

    <div class="card pt-5 mb-5">
        <form action="{{ route('dashboard.stock.store', [$merchant, $warehouse]) }}" method="POST">
            @csrf
            <div class="card-body pt-3">
                <div class="mb-10">
                    <h5>Детали:</h5>
                    <div class="d-flex flex-wrap py-5">
                        <div class="flex-equal me-5">
                            <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                <tbody>
                                <tr>
                                    <td class="text-gray-500">Имя мерчанта:</td>
                                    <td class="text-gray-800">{{$merchant->user->name}}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">Email мерчанта:</td>
                                    <td class="text-gray-800">{{$merchant->user->email}}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">Адрес:</td>
                                    <td class="text-gray-800">{{$merchant->address}}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">Телефон:</td>
                                    <td class="text-gray-800">{{$merchant->user->phone}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex-equal">
                            <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                <tbody><tr>
                                    <td class="text-gray-500 min-w-175px w-175px">Склад:</td>
                                    <td class="text-gray-800 min-w-200px">
                                        <a href="#" class="text-gray-800 text-hover-primary">{{$warehouse->name}}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">Адрес склада:</td>
                                    <td class="text-gray-800">{{$warehouse->address}}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">Принял:</td>
                                    <td class="text-gray-800">{{auth()->user()->name}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    @php
                        $totalQuantity = array_sum(array_column($data['products'], 'quantity'));
                    @endphp
                    <h5 class="mb-2">Количество товаров: {{ count($data['products']) }}</h5>
                    <h5 class="mb-4">Всего единиц: {{ $totalQuantity }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                            <thead>
                            <tr class="border-bottom border-gray-200 text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>#</th>
                                <th class="min-w-150px">Название</th>
                                <th class="min-w-125px">Кол-во</th>
                                <th class="text-end min-w-70px">Артикул</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold fs-5 text-gray-800">
                            @php $rowNumber = 1; @endphp
                            @foreach($data['products'] as $product)
                                <tr>
                                    <td>
                                        {{ $rowNumber++ }}
                                    </td>
                                    <td>
                                        <label class="min-w-150px">
                                            @if(!empty($product['new_product_name']))
                                                {{ $product['new_product_name'] }}
                                            @else
                                                {{ $productsMap[$product['product_id']]->name ?? '—' }}
                                            @endif
                                        </label>
                                    </td>
                                    <td class="">
                                        {{ $product['quantity'] }}
                                    </td>
                                    <td class="text-end">
                                        @if(empty($product['new_product_name']))
                                            {{ $productsMap[$product['product_id']]->sku }}
                                        @else
                                            <span class="badge badge-light-primary">
                                                Новый товар
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard.stock.create', [$merchant, $warehouse]) }}?from_preview=1" class="btn btn-light-primary">
                        Назад
                    </a>

                    <button type="submit" class="btn btn-success">
                        Подтвердить
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
