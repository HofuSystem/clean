@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
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
                        <li class="breadcrumb-item text-muted">@lang('info')</li>
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
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">


                    <form class="form"  id="form"
                        redirect-to="{{ route('dashboard.districts.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.districts.edit', $item->id) }}"
                            method="PUT"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.districts.create') }}"
                            method="POST"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="slug">{{ trans('slug') }}</label>
                                <input type="text" name="slug" class="form-control "
                                    placeholder="{{ trans('Enter slug') }} " value="{{ old('slug', $item->slug ?? null) }}">

                            </div>
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="city_id">{{ trans('city') }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id">

                                    <option value="">{{ trans('select city') }}</option>
                                    @foreach ($cities ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->city_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <button class="btn btn-danger" id="clearPoints">Clear All</button>

                            <div id="map"></div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>



                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />

    <style>
        #map {
            flex-grow: 1;
            height: 500px;
        }
        .leaflet-control-geocoder {
            margin-top: 10px !important;
        }
    </style>
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>

    <script>
        $(document).ready(function () {
            // Store all created polygons
            let oldPoints = {!! json_encode($points) !!};
            let coordinates = [];
            let allPolygons = [];
            let points      = [];
            let markers     = [];
            let polyline    = null;
            let polygon     = null;
            let autoPolygon = null;
            let currentColorIndex = 0;
            
            // Initialize the map
            const map = L.map('map').setView([25.624262, 42.352833], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Color palette for multiple areas
            const colorPalette = [{
                    border: '#4CAF50',
                    fill: '#81C784'
                },
                {
                    border: '#2196F3',
                    fill: '#64B5F6'
                },
                {
                    border: '#9C27B0',
                    fill: '#BA68C8'
                },
                {
                    border: '#FF9800',
                    fill: '#FFB74D'
                },
                {
                    border: '#F44336',
                    fill: '#E57373'
                }
            ];

            // Custom marker icon
            const markerIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
            });

            // Add the geocoder control to the map
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: 'Search for a location...',
                errorMessage: 'Nothing found.',
                showResultIcons: true
            }).on('markgeocode', function(e) {
                const bbox = e.geocode.bbox;
                const poly = L.polygon([
                    bbox.getSouthEast(),
                    bbox.getNorthEast(),
                    bbox.getNorthWest(),
                    bbox.getSouthWest()
                ]);
                map.fitBounds(poly.getBounds());
                
                // Add a marker at the found location
                const latlng = e.geocode.center;
                map.setView(latlng, 16);
            }).addTo(map);

            // Map click handler
            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);
                addPoint([lat, lng]);
            });

            // Clear points button handler
            $('#clearPoints').on('click', function(e) {
                e.preventDefault();
                clearAll();
            });

            function addPoint(coordinates) {
                const [lat, lng] = coordinates;

                // Add draggable marker
                const marker = L.marker([parseFloat(lat), parseFloat(lng)], {
                    draggable: true,
                    icon: markerIcon
                }).addTo(map);

                // Handle marker drag events
                marker.on('drag', function() {
                    updatePolygon();
                });

                marker.on('dragend', function() {
                    updatePointsList();
                });

                // Add click handler for marker deletion with confirmation
                marker.on('click', function() {
                    Swal.fire({
                        title: trans('Delete Point?'),
                        text: trans('Are you sure you want to delete this point?'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deletePoint(marker);
                        }
                    });
                });

                // Add double-click handler for immediate deletion
                marker.on('dblclick', function(e) {
                    L.DomEvent.stopPropagation(e); // Prevent map zoom
                    deletePoint(marker);
                });

                markers.push(marker);
                points.push([lat, lng]);

                updatePolygon();
                updatePointsList();
            }

            function deletePoint(marker) {
                // Find and remove the marker from markers array
                const index = markers.indexOf(marker);
                if (index > -1) {
                    markers.splice(index, 1);
                    points.splice(index, 1);
                    map.removeLayer(marker);
                }

                // Update the polygon and points list
                updatePolygon();
                updatePointsList();
            }

            function updatePolygon() {
                // Get current positions of all markers
                points = markers.map(marker => {
                    const pos = marker.getLatLng();
                    return [pos.lat.toFixed(6), pos.lng.toFixed(6)];
                });

                // Update polyline while drawing
                if (polyline) {
                    map.removeLayer(polyline);
                }
                polyline = L.polyline(points.map(p => [parseFloat(p[0]), parseFloat(p[1])]), {
                    color: colorPalette[currentColorIndex].border,
                    weight: 3,
                    opacity: 0.8
                }).addTo(map);

                // Auto-update polygon while drawing (if at least 3 points)
                if (points.length >= 3) {
                    if (autoPolygon) {
                        map.removeLayer(autoPolygon);
                    }
                    autoPolygon = L.polygon(points.map(p => [parseFloat(p[0]), parseFloat(p[1])]), {
                        color: colorPalette[currentColorIndex].border,
                        weight: 2,
                        fillColor: colorPalette[currentColorIndex].fill,
                        fillOpacity: 0.3,
                        dashArray: '5, 10'
                    }).addTo(map);
                }
            }

            function finishDrawing() {
                if (points.length < 3) {
                    Swal.fire({
                        text: "Please add at least 3 points to create an area",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    return;
                }

                // Remove temporary drawing elements
                if (polyline) {
                    map.removeLayer(polyline);
                    polyline = null;
                }
                if (autoPolygon) {
                    map.removeLayer(autoPolygon);
                    autoPolygon = null;
                }

                // Create final polygon
                if (polygon) {
                    map.removeLayer(polygon);
                }

                // Store the points for this polygon
                const polygonPoints = points.map(p => [parseFloat(p[0]), parseFloat(p[1])]);
                
                polygon = L.polygon(polygonPoints, {
                    color: colorPalette[currentColorIndex].border,
                    weight: 2,
                    fillColor: colorPalette[currentColorIndex].fill,
                    fillOpacity: 0.4,
                    dashArray: '5, 10'
                }).addTo(map);

                // Add the polygon points to our collection
                allPolygons.push(polygonPoints);

                // Add hover effect to polygon
                polygon.on('mouseover', function() {
                    $(this).setStyle({
                        fillOpacity: 0.6
                    });
                });

                polygon.on('mouseout', function() {
                    $(this).setStyle({
                        fillOpacity: 0.4
                    });
                });

                // Cycle to next color for next shape
                currentColorIndex = (currentColorIndex + 1) % colorPalette.length;

                // Clear current points and markers for the next area
                $.each(markers, function(index, marker) {
                    map.removeLayer(marker);
                });
                markers = [];
                points = [];
                updatePointsList();
            }

            function clearAll() {
                // Clear markers
                $.each(markers, function(index, marker) {
                    map.removeLayer(marker);
                });
                markers = [];

                // Clear all polygons and lines
                if (polyline) {
                    map.removeLayer(polyline);
                    polyline = null;
                }
                if (polygon) {
                    map.removeLayer(polygon);
                    polygon = null;
                }
                if (autoPolygon) {
                    map.removeLayer(autoPolygon);
                    autoPolygon = null;
                }

                // Clear points array and all polygons
                points = [];
                allPolygons = [];

                updatePointsList();
            }

            function updatePointsList() {
                coordinates = [];
                $.each(points, function(index, point) {
                    coordinates.push({
                        lat: point[0],
                        lng: point[1]
                    });
                });
            }

            // Handle form submission
            $('#form').on('submit', function(e) {
                e.preventDefault();

                // Get all form data
                const formData =getFormData($(this));

                // Add the polygon coordinates to the form data
                formData.coordinates = coordinates;
                console.log(formData,coordinates);

                // Get the form action URL and redirect URL
                const actionUrl = $(this).attr('action');
                const method = $(this).attr('method');
                // Send the AJAX request
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function(result) {
                            window.location.href = "{{ route('dashboard.districts.index') }}";
                        });
                    },
                    error: function(xhr, status, error) {
                        response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            });

            // Function to predraw an area from given points
            function predrawArea(coordinatesArray) {
                clearAll();

                $.each(coordinatesArray, function(index, coord) {
                    addPoint(coord);
                });
                // If there are points, center map on first point
                if (coordinatesArray.length > 0) {
                    const firstPoint = coordinatesArray[0];
                    map.setView([firstPoint[0], firstPoint[1]], 13);
                }
            }

            // Initialize with example area
            predrawArea(oldPoints);
        });
    </script>
@endpush
