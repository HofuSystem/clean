@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ trans('client.address_details') }}</h1>
            <p class="page-subtitle">
            </p>
        </div>

        <!-- Address Section -->
        <div class="address-section">


            <!-- Shipping Addresses -->
            <div class="shipping-addresses">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="section-title">
                        {{ trans('client.shipping_addresses') }}
                    </h3>
                    <div class="add-address-section mb-4">
                        <button class="add-address-btn">
                            {{ trans('client.add_address') }}
                        </button>
                    </div>
                </div>

                <div class="addresses-list">
                    @foreach ($addresses as $address)
                    <!-- Address Item 1 -->
                    <div class="address-item {{ $address->status == 'active' ? 'active' : 'not-active' }}" data-id="{{ $address->id }}">
                        <div class="address-content">
                            <div class="address-info">
                                <h4 class="address-title">
                                    <ion-icon name="location"></ion-icon>
                                    {{ $address->name }}
                                    @if ($address->status == 'active')
                                           <span class="text-success"> ({{ __('client.active') }})</span>
                                    @else
                                            <span class="text-danger"> ({{ __('client.not_active') }})</span>
                                    @endif
                                </h4>
                                <p class="address-description">{{ $address->location }}</p>
                            </div>
                        </div>
                        <div class="address-actions">
                            {{-- <button class="edit-btn">{{ trans('client.edit') }}</button> --}}
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $address->lat }},{{ $address->lng }}" target="_blank" class="show-on-maps-btn">
                                {{ trans('client.show_on_maps') }}
                            </a>
                            <form action="{{ route('client.address.delete', $address->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="delete-btn" data-id="{{ $address->id }}" data-name="{{ $address->name }}">{{ trans('client.delete') }}</button>
                            </form>
                        </div>
                    </div>                   
                    @endforeach
                    @if ($addresses->isEmpty())
                    <div class="address-item">
                        <p class="address-description">{{ trans('client.please_add_address') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </main>
    <!-- Address Modal -->
    <div class="address-modal-backdrop" id="addressModal">
        <div class="address-modal">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('client.add_shipping_address') }}</h3>
                <button type="button" class="close-btn" id="closeModal">&times;</button>
            </div>

            <form class="modal-form" id="addressForm" method="POST" action="{{ route('client.address.store') }}">
                @csrf
                <div class="form-group">
                    <label for="addressName" class="form-label">{{ __('client.address_name_label') }}</label>
                    <input type="text" id="addressName" name="name" class="form-control custom" placeholder="{{ __('client.enter_address_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="mapContainer" class="form-label">{{ __('client.choose_location_on_map') }}</label>
                    <div id="mapContainer" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #eef0f4;"></div>
                    <div id="locationStatus" class="mt-2" style="font-size: 14px; font-weight: 500;"></div>
                </div>

                <div class="form-group">
                    <label for="addressText" class="form-label">{{ __('client.address') }}</label>
                    <input type="text" id="addressText" name="address_text" class="form-control custom" placeholder="{{ __('client.select_location_on_map') }}" readonly>
                </div>

                <div class="form-group">
                    <input type="hidden" id="selectedLat" name="lat">
                    <input type="hidden" id="selectedLng" name="lng">
                    <input type="hidden" id="selectedAddress" name="location">
                    <input type="hidden" id="selectedDistrictId" name="district_id">
                    <input type="hidden" id="selectedCityId" name="city_id">
                </div>

                <div class="modal-actions">
                    <button type="submit" class="confirm-btn" id="submitBtn" disabled>{{ __('client.confirm') }}</button>
                    <button type="button" class="cancel-btn" id="cancelModal">{{ __('client.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>

    <style>
        .address-item {
            border: 1px dashed #000000;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .address-item:hover {
            background-color: #f8f9fa;
        }
        .address-item .address-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .address-modal {
            max-width: 800px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px 15px;
            border-bottom: 1px solid #eef0f4;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        #locationStatus {
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-weight: 500;
            border: 1px solid;
        }
        
        #locationStatus.available {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        #locationStatus.not-available {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        #locationStatus.searching {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        
        .confirm-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eef0f4;
        }
        
        .confirm-btn, .cancel-btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .confirm-btn {
            background: #28a745;
            color: white;
            border: none;
        }
        
        .confirm-btn:hover:not(:disabled) {
            background: #218838;
        }
        
        .cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
        }
        
        .cancel-btn:hover {
            background: #5a6268;
        }
        
        /* Google Maps styling */
        #mapContainer {
            border-radius: 8px;
            border: 1px solid #eef0f4;
            position: relative;
        }
        
        /* Search box styling */
        #mapSearchBox {
            background: white !important;
            border: 1px solid #eef0f4 !important;
            border-radius: 6px !important;
            padding: 10px 12px !important;
            font-size: 14px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            z-index: 1000 !important;
        }
        
        #mapSearchBox:focus {
            outline: none !important;
            border-color: #007bff !important;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25) !important;
        }
        
        .pac-container {
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            border: 1px solid #eef0f4 !important;
            margin-top: 2px !important;
            z-index: 10000 !important;
            background: white !important;
            max-height: 300px !important;
            overflow-y: auto !important;
        }
        
        .pac-item {
            padding: 12px 16px !important;
            border-bottom: 1px solid #f0f0f0 !important;
            font-size: 14px !important;
            line-height: 1.4 !important;
            cursor: pointer !important;
            transition: background-color 0.2s ease !important;
        }
        
        .pac-item:hover {
            background-color: #f8f9fa !important;
        }
        
        .pac-item:last-child {
            border-bottom: none !important;
        }
        
        .pac-item-selected {
            background-color: #007bff !important;
            color: white !important;
        }
        
        .pac-item-query {
            font-weight: 600 !important;
            color: #333 !important;
        }
        
        .pac-item-query.pac-item-selected {
            color: white !important;
        }
        
        .pac-matched {
            font-weight: 600 !important;
            color: #007bff !important;
        }
        
        .pac-matched.pac-item-selected {
            color: white !important;
        }
    </style>

    <script>
        const coveredAreas = {!! $coveredAreas !!};
        
        let map, marker, geocoder, autocomplete;
        let selectedPoint = null;
        let coveredArea = null;

        // Initialize map when modal opens
        document.querySelector('.add-address-btn').addEventListener('click', function() {
            document.getElementById('addressModal').style.display = 'flex';
            setTimeout(initMap, 100);
        });

        document.getElementById('cancelModal').addEventListener('click', function() {
            document.getElementById('addressModal').style.display = 'none';
            resetForm();
        });

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('addressModal').style.display = 'none';
            resetForm();
        });

        function initMap() {
            if (map) return; // Already initialized

            // Initialize the map
            const mapOptions = {
                center: { lat: 24.7136, lng: 46.6753 }, // Default to Saudi Arabia
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true
            };

            map = new google.maps.Map(document.getElementById('mapContainer'), mapOptions);

            // Initialize geocoder
            geocoder = new google.maps.Geocoder();

            // Add search box functionality
            const searchBox = document.createElement('input');
            searchBox.type = 'text';
            searchBox.id = 'mapSearchBox';
            searchBox.placeholder = '{{ __("client.search_location") }}';
            searchBox.style.cssText = `
                position: absolute;
                top: 10px;
                left: 10px;
                width: 300px;
                height: 40px;
                padding: 10px 12px;
                border: 1px solid #eef0f4;
                border-radius: 6px;
                font-size: 14px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                z-index: 1000;
                background: white;
            `;

            map.controls[google.maps.ControlPosition.TOP_LEFT].push(searchBox);

            // Add a label for the search box
            const searchLabel = document.createElement('div');
            searchLabel.textContent = '{{ __("client.search_location") }}';
            searchLabel.style.cssText = `
                position: absolute;
                top: -25px;
                left: 10px;
                font-size: 12px;
                color: #666;
                background: white;
                padding: 2px 6px;
                border-radius: 3px;
                z-index: 1001;
            `;
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(searchLabel);



            // Initialize autocomplete with improved settings
            autocomplete = new google.maps.places.Autocomplete(searchBox, {
                types: ['geocode', 'establishment'],
                componentRestrictions: { country: ['sa', 'ae', 'eg', 'kw', 'bh', 'om', 'qa'] }, // Middle East countries
                fields: ['formatted_address', 'geometry', 'name', 'place_id', 'types'],
                strictBounds: false
            });

            // Add debugging for autocomplete
            console.log('Autocomplete initialized');
            
            // Add input event listener for better responsiveness
            searchBox.addEventListener('input', function() {
                console.log('Search box input:', this.value);
                if (this.value.length >= 2) {
                    // Trigger autocomplete suggestions
                    google.maps.event.trigger(autocomplete, 'focus');
                }
            });

            // Add focus event to show suggestions immediately
            searchBox.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    google.maps.event.trigger(autocomplete, 'focus');
                }
            });

            // Add Enter key handler for manual search fallback
            searchBox.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        console.log('Manual search for:', query);
                        geocoder.geocode({ address: query }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                const place = results[0];
                                console.log('Manual search result:', place);
                                
                                // Remove existing marker
                                if (marker) {
                                    marker.setMap(null);
                                }

                                // Add new marker
                                marker = new google.maps.Marker({
                                    map: map,
                                    position: place.geometry.location,
                                    draggable: true
                                });

                                // Add drag event listener to marker
                                marker.addListener('dragend', function() {
                                    const position = marker.getPosition();
                                    const lat = position.lat();
                                    const lng = position.lng();
                                    
                                    document.getElementById('selectedLat').value = lat;
                                    document.getElementById('selectedLng').value = lng;
                                    
                                    getAddressFromCoordinates(lat, lng);
                                    checkCoveredArea(lat, lng);
                                });

                                // Update form fields
                                const lat = place.geometry.location.lat();
                                const lng = place.geometry.location.lng();
                                
                                document.getElementById('selectedLat').value = lat;
                                document.getElementById('selectedLng').value = lng;
                                document.getElementById('selectedAddress').value = place.formatted_address;
                                document.getElementById('addressText').value = place.formatted_address;

                                // Check if point is in covered area
                                setTimeout(() => {
                                    checkCoveredArea(lat, lng);
                                }, 100);

                                // Fit map to place
                                if (place.geometry.viewport) {
                                    map.fitBounds(place.geometry.viewport);
                                } else {
                                    map.setCenter(place.geometry.location);
                                    map.setZoom(15);
                                }
                            } else {
                                console.error('Geocoding failed:', status);
                                alert('{{ __("client.location_not_found") }}');
                            }
                        });
                    }
                }
            });

            // Handle autocomplete selection
            autocomplete.addListener('place_changed', function() {
                console.log('Place changed event triggered');
                const place = autocomplete.getPlace();
                console.log('Selected place:', place);
                
                if (!place.geometry) {
                    console.log("No geometry found for place");
                    return;
                }

                // Remove existing marker
                if (marker) {
                    marker.setMap(null);
                }

                // Add new marker
                marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    draggable: true
                });

                // Add drag event listener to marker
                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    const lat = position.lat();
                    const lng = position.lng();
                    
                    document.getElementById('selectedLat').value = lat;
                    document.getElementById('selectedLng').value = lng;
                    
                    getAddressFromCoordinates(lat, lng);
                    checkCoveredArea(lat, lng);
                });

                // Update form fields
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                
                document.getElementById('selectedLat').value = lat;
                document.getElementById('selectedLng').value = lng;
                document.getElementById('selectedAddress').value = place.formatted_address;
                document.getElementById('addressText').value = place.formatted_address;

                // Check if point is in covered area
                setTimeout(() => {
                    checkCoveredArea(lat, lng);
                }, 100);

                // Fit map to place
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                }
            });

            // Handle map clicks
            map.addListener('click', function(e) {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                
                console.log('Map clicked at:', lat, lng);
                
                // Remove existing marker
                if (marker) {
                    marker.setMap(null);
                }
                
                // Add new marker
                marker = new google.maps.Marker({
                    map: map,
                    position: e.latLng,
                    draggable: true
                });

                // Add drag event listener to marker
                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    const lat = position.lat();
                    const lng = position.lng();
                    
                    document.getElementById('selectedLat').value = lat;
                    document.getElementById('selectedLng').value = lng;
                    
                    getAddressFromCoordinates(lat, lng);
                    checkCoveredArea(lat, lng);
                });
                
                // Update form fields
                document.getElementById('selectedLat').value = lat;
                document.getElementById('selectedLng').value = lng;
                
                // Get address from coordinates first, then check coverage
                getAddressFromCoordinates(lat, lng);
                
                // Check if point is in covered area after a short delay
                setTimeout(() => {
                    checkCoveredArea(lat, lng);
                }, 500);
            });

            // Note: Marker drag events will be handled when markers are created
        }

        function getAddressFromCoordinates(lat, lng) {
            const statusDiv = document.getElementById('locationStatus');
            const currentStatus = statusDiv.textContent;
            
            // Only show searching status if no coverage status is currently displayed
            if (!currentStatus.includes('{{ __("client.available_for_delivery") }}') && 
                !currentStatus.includes('{{ __("client.not_available_for_delivery") }}')) {
                statusDiv.textContent = '{{ __("client.searching_for_address") }}';
                statusDiv.className = 'searching';
            }
            
            const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
            
            geocoder.geocode({ location: latlng }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        const address = results[0].formatted_address;
                        console.log('Setting address to:', address);
                        document.getElementById('selectedAddress').value = address;
                        document.getElementById('addressText').value = address;
                        
                        // Don't clear status if coverage check is in progress or completed
                        const currentStatus = statusDiv.textContent;
                        if (!currentStatus.includes('{{ __("client.available_for_delivery") }}') && 
                            !currentStatus.includes('{{ __("client.not_available_for_delivery") }}') &&
                            !currentStatus.includes('{{ __("client.checking_coverage") }}')) {
                            statusDiv.textContent = '';
                            statusDiv.className = '';
                        }
                    } else {
                        // Fallback to coordinates
                        const coordAddress = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        console.log('Using coordinates as address:', coordAddress);
                        document.getElementById('selectedAddress').value = coordAddress;
                        document.getElementById('addressText').value = coordAddress;
                        
                        // Don't clear status if coverage check is in progress or completed
                        const currentStatus = statusDiv.textContent;
                        if (!currentStatus.includes('{{ __("client.available_for_delivery") }}') && 
                            !currentStatus.includes('{{ __("client.not_available_for_delivery") }}') &&
                            !currentStatus.includes('{{ __("client.checking_coverage") }}')) {
                            statusDiv.textContent = '';
                            statusDiv.className = '';
                        }
                    }
                } else {
                    console.error('Geocoder failed due to:', status);
                    // Fallback to coordinates
                    const coordAddress = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    console.log('Error fallback to coordinates:', coordAddress);
                    document.getElementById('selectedAddress').value = coordAddress;
                    document.getElementById('addressText').value = coordAddress;
                    
                    // Don't clear status if coverage check is in progress or completed
                    const currentStatus = statusDiv.textContent;
                    if (!currentStatus.includes('{{ __("client.available_for_delivery") }}') && 
                        !currentStatus.includes('{{ __("client.not_available_for_delivery") }}') &&
                        !currentStatus.includes('{{ __("client.checking_coverage") }}')) {
                        statusDiv.textContent = '';
                        statusDiv.className = '';
                    }
                }
            });
        }

        function checkCoveredArea(lat, lng) {
            const statusDiv = document.getElementById('locationStatus');
            const submitBtn = document.getElementById('submitBtn');
            
            // Clear any existing status and show checking message
            statusDiv.textContent = '{{ __("client.checking_coverage") }}';
            statusDiv.className = 'searching';
            
            // Check if point is within any covered area
            let foundArea = null;
            
            coveredAreas.forEach(district => {
                if (district.map_points && district.map_points.length > 0) {
                    // Create polygon from map points
                    const points = district.map_points.map(point => [parseFloat(point.lat), parseFloat(point.lng)]);
                    
                    if (points.length >= 3) {
                        // Check if point is inside polygon
                        if (isPointInPolygon([lat, lng], points)) {
                            foundArea = district;
                        }
                    }
                }
            });
            
            if (foundArea) {
                statusDiv.textContent = `✓ {{ __('client.available_for_delivery') }} - ${foundArea.name}`;
                statusDiv.className = 'available';
                submitBtn.disabled = false;
                coveredArea = foundArea;
                
                // Set district and city IDs
                document.getElementById('selectedDistrictId').value = foundArea.id;
                document.getElementById('selectedCityId').value = foundArea.city_id;
            } else {
                statusDiv.textContent = '✗ {{ __("client.not_available_for_delivery") }}';
                statusDiv.className = 'not-available';
                submitBtn.disabled = true;
                coveredArea = null;
                
                // Clear district and city IDs
                document.getElementById('selectedDistrictId').value = '';
                document.getElementById('selectedCityId').value = '';
            }
        }

        // Point in polygon algorithm
        function isPointInPolygon(point, polygon) {
            const x = point[0];
            const y = point[1];
            let inside = false;
            
            for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                const xi = polygon[i][0];
                const yi = polygon[i][1];
                const xj = polygon[j][0];
                const yj = polygon[j][1];
                
                if (((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi)) {
                    inside = !inside;
                }
            }
            
            return inside;
        }

        function resetForm() {
            document.getElementById('addressForm').reset();
            clearStatus();
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('addressText').value = '';
            
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
            
            selectedPoint = null;
            coveredArea = null;
        }

        function clearStatus() {
            const statusDiv = document.getElementById('locationStatus');
            statusDiv.textContent = '';
            statusDiv.className = '';
        }

        // Handle form submission
        document.getElementById('addressForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const addressName = document.getElementById('addressName').value;
            const lat = document.getElementById('selectedLat').value;
            const lng = document.getElementById('selectedLng').value;
            const address = document.getElementById('selectedAddress').value;
            const districtId = document.getElementById('selectedDistrictId').value;
            const cityId = document.getElementById('selectedCityId').value;
            
            if (!addressName || !lat || !lng || !coveredArea) {
                alert('{{ __("client.please_fill_all_fields") }}');
                return;
            }
            
            // Submit the form
            this.submit();
        });
    </script>
@endsection
