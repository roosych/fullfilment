@extends('layouts.dashboard')

@section('title', 'Администраторы')

@section('breadcrumbs')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard.index')}}" class="text-muted text-hover-primary">
                Панель управления
            </a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            Администраторы
        </li>
    </ul>
@endsection

@section('actions')
    <div class="d-flex align-items-center gap-3 gap-lg-5">
        <a href="{{route('dashboard.admins.create')}}" class="btn btn-sm btn-flex btn-center btn-dark px-4">
            <i class="ki-duotone ki-plus-square fs-2 p-0 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            Добавить администратора
        </a>
    </div>
@endsection

@section('content')
    @if(session('alert'))
        <x-alert :type="session('alert.type')" :message="session('alert.message')"/>
    @endif

    <div class="card">
        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="admins_table">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">{{__('Name')}}</th>
                    <th class="text-center">{{__('Email')}}</th>
                    <th class="text-center">{{__('Phone')}}</th>
                    <th class="text-end">{{__('Status')}}</th>
                    <th class="text-end">{{__('Created At')}}</th>
                </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                @forelse($admins as $admin)
                    <tr>
                        <td class="d-flex align-items-center border-bottom-0">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                <div class="symbol-label fs-3 bg-light-primary text-primary">
                                    {{ mb_substr($admin->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 text-hover-primary mb-1">
                                    {{$admin->name}}
                                </span>
                            </div>
                        </td>
                        <td class="text-center pe-0">
                            <span class="text-gray-800">{{$admin->email}}</span>
                        </td>
                        <td class="text-center pe-0">
                            <span class="text-gray-800">{{$admin->phone}}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-check-custom form-switch">
                                    <input class="form-check-input w-35px h-20px admin-status-toggle"
                                           type="checkbox"
                                           id="activeSwitch{{$admin->id}}"
                                           data-user-id="{{$admin->id}}"
                                           data-url="{{ route('dashboard.admins.toggle-status', $admin) }}"
                                        {{$admin->active ? 'checked' : ''}}
                                        {{ $admin->id === auth()->id() ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="activeSwitch{{$admin->id}}"></label>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="text-gray-600">{{$admin->created_at->format('d.m.Y H:i')}}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10">
                            <span class="text-gray-500">Администраторы не найдены</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('vendor_css')

@endpush

@push('vendor_js')
    <script src="{{asset('assets/js/plugins/datatables.bundle.js')}}"></script>
@endpush

@push('custom_js')
    <script>
        $(document).ready(function() {
            const table = $('#admins_table').DataTable({
                pageLength: 25,
                order: [[4, 'desc']],
            });

            // Обработка переключения статуса админа
            $(document).on('change', '.admin-status-toggle', function() {
                const $checkbox = $(this);
                const userId = $checkbox.data('user-id');
                const url = $checkbox.data('url');
                const isChecked = $checkbox.is(':checked');

                // Сохраняем текущее состояние для возможного отката
                const previousState = !isChecked;

                // Блокируем чекбокс во время запроса
                $checkbox.prop('disabled', true);

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Обновляем состояние чекбокса
                            $checkbox.prop('checked', response.active);
                            
                            // Показываем уведомление об успехе, если Swal доступен
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Успешно',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        // Откатываем состояние чекбокса при ошибке
                        $checkbox.prop('checked', previousState);
                        
                        const message = xhr.responseJSON?.message || 'Произошла ошибка при обновлении статуса';
                        
                        // Показываем уведомление об ошибке, если Swal доступен
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ошибка',
                                text: message,
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            alert(message);
                        }
                    },
                    complete: function() {
                        // Разблокируем чекбокс после завершения запроса
                        // Но только если это не текущий пользователь
                        const currentUserId = {{ auth()->id() }};
                        if (parseInt(userId) !== currentUserId) {
                            $checkbox.prop('disabled', false);
                        }
                    }
                });
            });
        });
    </script>
@endpush

