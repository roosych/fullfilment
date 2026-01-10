@extends('layouts.dashboard')

@section('title', 'Детали пополнения - ' . $stockBatch->batch_code)

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('cabinet.dashboard')}}" class="text-muted text-hover-primary">
                Главная
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{route('cabinet.stock.index')}}" class="text-muted text-hover-primary">
                Пополнения склада
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            {{$stockBatch->batch_code}}
        </li>
    </ul>
@endsection

@section('content')
    <div class="card">
        <div class="card-body py-20">
            <div class="mw-lg-950px mx-auto w-100">
                <div class="d-flex justify-content-between flex-column flex-sm-row mb-19">
                    <h4 class="fw-bolder text-gray-800 fs-2qx pe-5 pb-7">
                        Накладная на получение товара
                    </h4>

                    <div class="text-sm-end">
                        <a href="#">
                            <img class="w-25" alt="Logo" src="{{asset('assets/img/favicon.png')}}">
                        </a>
                        <div class="text-sm-end fw-semibold fs-4 text-muted mt-7">
                            <div>"WDEX" LLC, Sarayevo street 18, 251</div>
                            <div>Azerbaijan, Baku</div>
                        </div>
                    </div>
                </div>

                <div class="border-bottom pb-12">
                    <div class="d-flex justify-content-between flex-column flex-md-row">
                        <div class="flex-grow-1 pt-8 mb-13">
                            <div class="table-responsive border-bottom mb-14">
                                <table class="table">
                                    <thead>
                                    <tr class="border-bottom fs-6 fw-bold text-muted text-uppercase">
                                        <th class="pb-9">#</th>
                                        <th class="min-w-175px pb-9">Товар</th>
                                        <th class="min-w-80px pb-9 text-end">SKU</th>
                                        <th class="min-w-70px pb-9 text-end">Количество</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($stockBatch->entries as $entry)
                                        <tr class="fw-bold text-gray-700 fs-5 text-end">
                                            <td class="text-start pt-6">{{$loop->index + 1}}</td>
                                            <td class="d-flex align-items-center pt-6">
                                                {{ $entry->product->name }}
                                            </td>
                                            <td class="pt-6">{{ $entry->product->sku }}</td>
                                            <td class="pt-6">{{ $entry->quantity }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-column mw-md-300px w-100">
                                <div class="fw-semibold fs-5 mb-3 text-gray-90000">ПОЛУЧЕНО</div>
                                <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                    <div class="fw-semibold pe-5">Склад:</div>
                                    <div class="text-end fw-norma">{{$stockBatch->warehouse->name ?? '—'}}</div>
                                </div>
                                <div class="d-flex flex-stack text-gray-800 fs-6 mt-10">
                                    <div class="fw-semibold pe-5">Подпись:</div>
                                    <div class="text-end fw-norma">__________________</div>
                                </div>
                            </div>
                        </div>

                        <div class="border-end d-none d-md-block mh-450px mx-9"></div>

                        <div class="text-end pt-10">
                            <div class="fs-3 fw-bold text-muted mb-3">ПАРТИЯ</div>
                            <div class="fs-xl-2x fs-2 mb-2 fw-bolder">{!! DNS1D::getBarcodeHTML($stockBatch->batch_code, 'C128', 2, 60, 'black', true) !!}</div>
                            <div class="fs-5 fw-semibold">{{$stockBatch->batch_code}}</div>

                            <div class="border-bottom w-100 my-7 my-lg-16"></div>

                            <div class="text-gray-600 fs-6 fw-semibold mb-3">МЕРЧАНТ</div>
                            <div class="fs-6 text-gray-800 fw-semibold mb-8">
                                {{$stockBatch->merchant->user->name}}
                                <br>{{$stockBatch->merchant->company}}</div>

                            <div class="text-gray-600 fs-6 fw-semibold mb-3">НОМЕР НАКЛАДНОЙ</div>
                            <div class="fs-6 text-gray-800 fw-semibold mb-8">{{$stockBatch->batch_code}}</div>
                            <div class="text-gray-600 fs-6 fw-semibold mb-3">ДАТА</div>
                            <div class="fs-6 text-gray-800 fw-semibold">{{\Carbon\Carbon::parse($stockBatch->received_at)->isoFormat('DD MMM Y, HH:mm')}}</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-stack flex-wrap mt-lg-20 pt-13">
                    <div class="my-1 me-5">
                        <button type="button" class="btn btn-success my-1 me-12" onclick="window.print();">Печать</button>
                        <a href="{{ route('cabinet.stock.index') }}" class="btn btn-light my-1">Назад</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

