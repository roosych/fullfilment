@extends('layouts.dashboard')

@section('title', 'Зоны доставки')

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
            Тарифы доставки
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

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="row">
            <!-- Карта -->
            <div class="col-md-8">
                <div id="map" style="height: 450px;"></div>
            </div>

            <!-- Форма создания/редактирования зоны -->
            <div class="col-md-4">
            <h4>Добавить зону</h4>
            <form id="zone_form" action="{{route('dashboard.delivery-zones.store')}}" method="POST">
                @csrf
                <input type="hidden" name="polygon_coordinates" id="polygon_coordinates">

                <div class="mb-3">
                    <label for="name" class="form-label">Название зоны</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <button class="btn btn-primary mt-2">Сохранить зону</button>
            </form>

            <!-- Список зон и тарифов -->
            <h5 class="mt-4">Существующие зоны</h5>
                <ul class="list-group">
                    @foreach($zones as $zone)
                        <li class="list-group-item">
                            <strong>{{ $zone->name }}</strong>
                            <ul>
                                @foreach($zone->rates as $rate)
                                    <li>{{ $rate->name }}: {{ $rate->pivot->min_weight }}kg - {{ $rate->pivot->max_weight ?? '∞' }}kg → {{ $rate->pivot->price }}₼</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

@endsection

@push('vendor_css')

@endpush

@push('vendor_js')

@endpush

@push('custom_js')
    <!-- Leaflet & Leaflet Draw -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Инициализация карты
            var map = L.map('map').setView([40.4093, 49.8671], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Подгружаем существующие зоны
            var zones = @json($zones);
            zones.forEach(function(zone){
                var coords = JSON.parse(zone.polygon_coordinates);
                var polygon = L.polygon(coords, {color: 'blue'}).addTo(drawnItems);
                polygon.bindPopup(zone.name);
            });

            // Инициализация draw control
            var drawControl = new L.Control.Draw({
                edit: { featureGroup: drawnItems },
                draw: { polygon: true, marker: false, circle: false, rectangle: false, polyline: false, circlemarker: false }
            });
            map.addControl(drawControl);

            // Событие создания нового полигона
            map.on(L.Draw.Event.CREATED, function (event) {
                var layer = event.layer;
                drawnItems.addLayer(layer);

                // Сохраняем координаты в hidden поле формы
                var coords = layer.getLatLngs()[0].map(function(latlng){
                    return [latlng.lat, latlng.lng];
                });
                document.getElementById('polygon_coordinates').value = JSON.stringify(coords);
            });

            // Событие редактирования существующих полигонов
            map.on('draw:edited', function (event) {
                var layers = event.layers;
                layers.eachLayer(function(layer){
                    var coords = layer.getLatLngs()[0].map(function(latlng){
                        return [latlng.lat, latlng.lng];
                    });
                    document.getElementById('polygon_coordinates').value = JSON.stringify(coords);
                });
            });
        });
    </script>
@endpush
