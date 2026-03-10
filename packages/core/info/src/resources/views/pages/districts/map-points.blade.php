@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('info')</li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <!--begin::Card-->
                <div class="container">
                    <div class="sidebar">
                        <h1>View Areas</h1>
                        <button id="clearAreas">Clear All</button>
                        <div class="areas-list" id="areasList">
                            <!-- Areas will be listed here -->
                        </div>
                    </div>
                    <div id="map"></div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        .container {
            display: flex;
            height: calc(100vh - 200px);
            margin: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .sidebar {
            width: 320px;
            padding: 24px;
            background-color: #ffffff;
            border-right: 1px solid #eef0f4;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        h1 {
            font-size: 24px;
            margin: 0;
            color: #1e293b;
            font-weight: 600;
        }

        button {
            display: block;
            width: 100%;
            padding: 12px 16px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 14px;
        }

        button:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            background-color: #e2e8f0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #map {
            flex-grow: 1;
            height: 100%;
            z-index: 1;
        }

        .areas-list {
            flex-grow: 1;
            overflow-y: auto;
            margin-top: 10px;
        }

        .area-item {
            padding: 16px;
            background-color: #f8fafc;
            margin-bottom: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #eef0f4;
        }

        .area-item:hover, .area-item.active {
            background-color: #f1f5f9;
            transform: translateX(4px);
            border-color: #e2e8f0;
        }

        .area-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
            font-weight: 600;
        }

        .area-item span {
            font-size: 13px;
            color: #64748b;
        }

        /* Scrollbar styling */
        .areas-list::-webkit-scrollbar {
            width: 6px;
        }

        .areas-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .areas-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .areas-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Map controls styling */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .leaflet-control-zoom a {
            border-radius: 8px !important;
            color: #1e293b !important;
            transition: all 0.2s ease !important;
        }

        .leaflet-control-zoom a:hover {
            background-color: #f8fafc !important;
            color: #4f46e5 !important;
        }

        .leaflet-control-geocoder {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            border: none !important;
            border-radius: 8px !important;
        }

        .leaflet-control-geocoder input {
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
            border: 1px solid #eef0f4 !important;
        }
    </style>
@endpush
@push('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize the map
            const map = L.map('map').setView([51.505, -0.09], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // UI Elements
            const clearAreasBtn = document.getElementById('clearAreas');
            const areasList = document.getElementById('areasList');

            // State variables
            let areas = [];
            let polygons = new Map(); // Map to store area polygons

            // Dynamic color generation functions
            function hslToHex(h, s, l) {
                l /= 100;
                const a = s * Math.min(l, 1 - l) / 100;
                const f = n => {
                    const k = (n + h / 30) % 12;
                    const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
                    return Math.round(255 * color).toString(16).padStart(2, '0');
                };
                return `#${f(0)}${f(8)}${f(4)}`;
            }

            function generateColors(count) {
                const colors = [];
                const hueStep = 360 / count;

                for (let i = 0; i < count; i++) {
                    const hue = i * hueStep;
                    // Generate border color (more saturated)
                    const borderColor = hslToHex(hue, 60, 45);
                    // Generate fill color (lighter and less saturated)
                    const fillColor = hslToHex(hue, 45, 70);

                    colors.push({
                        border: borderColor,
                        fill: fillColor
                    });
                }

                return colors;
            }

            // Event Handlers
            clearAreasBtn.addEventListener('click', clearAreas);

            // Sample data with 10 different areas
            const areas_data = {!! $districts !!};

            // Load areas directly when the page opens
            loadAreas(areas_data);

            function loadAreas(newAreas) {
                try {
                    // Clear existing areas
                    clearAreas();

                    // Generate colors based on number of areas
                    const colors = generateColors(newAreas.length);

                    // Load new areas
                    areas = newAreas;
                    areas.forEach((area, index) => {
                        addAreaToMap(area, colors[index]);
                    });

                    // Fit map to show all areas
                    if (areas.length > 0) {
                        const bounds = L.latLngBounds(areas.flatMap(area => area.points));
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                } catch (error) {
                    console.error('Error loading areas:', error);
                }
            }

            function addAreaToMap(area, colors) {
                // Create polygon
                const polygon = L.polygon(area.points, {
                    color: colors.border,
                    weight: 2,
                    fillColor: colors.fill,
                    fillOpacity: 0.4,
                    dashArray: '5, 10'
                }).addTo(map);

                // Add hover effects
                polygon.on('mouseover', function () {
                    this.setStyle({
                        fillOpacity: 0.6
                    });
                    highlightAreaInList(area.name);
                });

                polygon.on('mouseout', function () {
                    this.setStyle({
                        fillOpacity: 0.4
                    });
                    unhighlightAreaInList(area.name);
                });

                // Store polygon reference
                polygons.set(area.name, polygon);

                // Add to list
                addAreaToList(area, colors);
            }

            function addAreaToList(area, colors) {
                const areaItem = document.createElement('div');
                areaItem.className = 'area-item';
                areaItem.innerHTML = `
            <strong style="color: ${colors.border}">${area.name}</strong>
            <br>
            Points: ${area.points.length}
        `;

                // Add hover interaction
                areaItem.addEventListener('mouseover', () => {
                    const polygon = polygons.get(area.name);
                    if (polygon) {
                        polygon.setStyle({
                            fillOpacity: 0.6
                        });
                    }
                    areaItem.classList.add('active');
                });

                areaItem.addEventListener('mouseout', () => {
                    const polygon = polygons.get(area.name);
                    if (polygon) {
                        polygon.setStyle({
                            fillOpacity: 0.4
                        });
                    }
                    areaItem.classList.remove('active');
                });

                // Add click to zoom
                areaItem.addEventListener('click', () => {
                    const polygon = polygons.get(area.name);
                    if (polygon) {
                        map.fitBounds(polygon.getBounds(), { padding: [50, 50] });
                    }
                });

                areasList.appendChild(areaItem);
            }

            function highlightAreaInList(areaName) {
                const items = areasList.getElementsByClassName('area-item');
                for (const item of items) {
                    if (item.querySelector('strong').textContent === areaName) {
                        item.classList.add('active');
                    }
                }
            }

            function unhighlightAreaInList(areaName) {
                const items = areasList.getElementsByClassName('area-item');
                for (const item of items) {
                    if (item.querySelector('strong').textContent === areaName) {
                        item.classList.remove('active');
                    }
                }
            }

            function clearAreas() {
                // Clear polygons from map
                polygons.forEach(polygon => map.removeLayer(polygon));
                polygons.clear();

                // Clear areas list
                areasList.innerHTML = '';
                areas = [];
            }
        });
    </script>
@endpush