@extends('layouts.dashboard')

@section('title', 'Edit Warehouse')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">
                Dashboard
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.warehouses.index') }}" class="text-muted text-hover-primary">
                Warehouses
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Edit Warehouse
        </li>
    </ul>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    @if ($errors->any())
        <x-alert type="danger" :message="implode('<br>', $errors->all())"/>
    @endif

    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column pt-3">
                <h3 class="fw-bold mb-1">Edit Warehouse</h3>
                <div class="fs-6 text-gray-500">Update the details below to modify this warehouse</div>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('dashboard.warehouses.update', $warehouse) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-6">
                    <div class="col-md-12 mb-4">
                        <label class="form-label required">Name</label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-solid"
                               value="{{ old('name', $warehouse->name) }}"
                               placeholder="Enter warehouse name"
                               required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text"
                               name="address"
                               class="form-control form-control-solid"
                               value="{{ old('address', $warehouse->address) }}"
                               placeholder="Enter address">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="form-label">Notes</label>
                    <textarea name="notes"
                              class="form-control form-control-solid"
                              rows="3"
                              placeholder="Additional details...">{{ old('notes', $warehouse->notes) }}</textarea>
                </div>

                <div class="form-check form-switch mb-8">
                    <input class="form-check-input"
                           type="checkbox"
                           name="active"
                           id="activeSwitch"
                        {{ old('active', $warehouse->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeSwitch">Active</label>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dashboard.warehouses.index') }}" class="btn btn-light me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
