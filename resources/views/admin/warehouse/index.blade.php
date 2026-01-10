@extends('layouts.dashboard')

@section('title', 'Warehouses')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">
                Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Warehouses
        </li>
    </ul>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

{{--
    <pre>{{ print_r($warehouses->toArray(), true) }}</pre>
--}}

    <div class="card mb-5 mb-xl-10">
        <div class="card-body p-8">
            <div class="row gx-9 gy-6">
                @foreach($warehouses as $warehouse)
                    <div class="col-xl-6">
                        <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                            <div class="d-flex flex-column py-2">
                                <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                    {{$warehouse->name}}
                                    @if($warehouse->is_primary)
                                        <span class="badge badge-light-success fs-7 ms-2">Primary</span>
                                    @endif
                                </div>

                                <div class="fs-6 fw-semibold text-gray-600">
                                    {{$warehouse->address}}
                                </div>
                                <div class="fs-6 fw-semibold text-gray-600">
                                    {{$warehouse->phone}}
                                </div>
                            </div>

                            <div class="d-flex align-items-center py-2">
                                @if(! $warehouse->is_primary)
                                    <form action="{{ route('dashboard.warehouses.setPrimary', $warehouse) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light-primary btn-active-primary me-3">
                                            <span class="indicator-label">Set primary</span>
                                        </button>
                                    </form>

                                    <button class="btn btn-sm btn-light btn-active-light-danger me-3">
                                        <span class="indicator-label">Delete</span>
                                    </button>
                                @endif
                                <a href="{{route('dashboard.warehouses.show', $warehouse)}}" class="btn btn-sm btn-light btn-active-light-primary">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-xl-6">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Warehouse Control</h4>
                                <div class="fs-6 text-gray-700 pe-7">
                                    Add, manage, and organize your storage locations easily.
                                </div>
                            </div>

                            <a href="{{route('dashboard.warehouses.create')}}" class="btn btn-primary px-6 align-self-center text-nowrap">
                                Add warehouse
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
