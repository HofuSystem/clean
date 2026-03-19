@extends('b2b::web.layouts.app')

@push('styles')
<!-- Leaflet Premium Styles -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    #branch-map {
        width: 100%;
        height: 300px !important;
        min-height: 300px !important;
        background-color: #f8fafc;
        border-radius: 24px;
        z-index: 1;
    }

    .leaflet-container {
        font-family: 'Tajawal', sans-serif;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet JS Stack -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
@endpush

@section('content')
<!-- VIEW: Branches -->
<div id="view-branches" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text text-right">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ $title }}</h2>
            <p class="text-gray-500 font-medium text-sm">{{ $description }}</p>
        </div>
        <button onclick="openBranchModal()"
            class="px-6 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            <span>{{ trans('client.add_branch') }}</span>
        </button>
    </div>

    @if($errors->any())
    <div class="bg-red-50 text-red-500 p-4 rounded-xl border border-red-100 text-sm font-medium">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 text-green-500 p-4 rounded-xl border border-green-100 text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
        id="branche-list-container">
        @forelse($branches as $branche)
        <div
            class="border border-gray-200 p-6 rounded-2xl bg-gray-50/50 flex justify-between items-center group hover:border-[#1c75bc]/50 transition-colors dir-dependent-flex mb-4">
            <div class="text-right dir-dependent-text">
                <div class="font-black text-gray-900 text-lg flex items-center gap-2 justify-start dir-dependent-flex">
                    <svg class="w-5 h-5 text-[#1c75bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                    </svg>
                    <span>{{ $branche->name }}</span>
                    @if($branche->is_default)
                    <span
                        class="bg-blue-100 text-[#1c75bc] text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{
                        trans('client.is_default') }}</span>
                    @endif
                    @if($branche->is_active)
                    <span
                        class="bg-green-100 text-[#1c75bc] text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{
                        trans('client.is_active') }}</span>
                    @else
                    <span class="bg-red-100 text-red-500 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{
                        trans('client.is_inactive') }}</span>
                    @endif
                </div>
                <div class="text-gray-500 text-sm mt-1 font-medium">{{ $branche->city->name ?? '' }}،
                    {{ $branche->district->name ?? '' }}
                </div>
                <div class="text-gray-400 text-xs mt-0.5">{{ $branche->location }}</div>
            </div>
            <div class="flex gap-2">
                @if($branche->lat && $branche->lng)
                <a href="https://www.google.com/maps?q={{ $branche->lat }},{{ $branche->lng }}" target="_blank"
                    class="hidden md:flex px-4 py-2 border border-blue-100 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 text-xs font-bold items-center">{{
                    trans('client.show_on_maps') }}</a>
                @endif
                <button onclick='openBranchModal(@json($branche))'
                    class="px-4 py-2 border border-gray-100 rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 text-xs font-bold">
                    {{ trans('client.edit') }}
                </button>
                <form action="{{ route('client.branches.delete', $branche->id) }}" method="POST"
                    onsubmit="return confirm('{{ trans('client.confirm_delete') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 border border-red-100 rounded-lg text-red-500 bg-red-50 hover:bg-red-100 text-xs font-bold">{{
                        trans('client.delete') }}</button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-12 text-center text-gray-400 font-bold">
            {{ trans('client.no_data') }}
        </div>
        @endforelse
    </div>
</div>

<!-- PREMIUM Add/Edit Branch Modal -->
<div id="branch-modal"
    class="modal-content hidden fixed inset-0 z-[100] outline-none focus:outline-none justify-center items-center flex overflow-x-hidden overflow-y-auto p-4 md:p-12"
    onclick="if(event.target === this) closeBranchModal()">

    <!-- Subtle Backdrop (Moved INSIDE to ensure z-index order) -->
    <div id="branch-modal-backdrop"
        class="fixed inset-0 bg-black/20 backdrop-blur-[2px] -z-10 pointer-events-none transition-opacity duration-300">
    </div>

    <div class="relative w-full max-w-2xl mx-auto transform transition-all duration-300 scale-95 origin-center"
        onclick="event.stopPropagation()">
        <form id="branch-form" method="POST" class="w-full">
            @csrf
            <div id="method-container"></div>
            <div
                class="bg-white/95 backdrop-blur-2xl rounded-[32px] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.2)] border border-white/50 flex flex-col w-full outline-none focus:outline-none overflow-hidden text-right dir-dependent-text">

                <!-- Modal Header -->
                <div
                    class="px-8 py-6 border-b border-gray-100 bg-gradient-to-b from-gray-50/50 to-white flex justify-between items-center rounded-t-[32px] dir-dependent-flex">
                    <button type="button"
                        class="p-2.5 bg-white border border-gray-200 text-gray-500 rounded-full hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition-all shadow-sm"
                        onclick="closeBranchModal()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight" id="modal-title">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#1c75bc] to-[#155a91]">
                            {{ trans('client.add_branch') }}
                        </span>
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-8 space-y-6">                    <!-- Name & City -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.branch_name') }} *
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1c75bc] transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="branch_name_input" required
                                    class="w-full pr-12 p-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800 shadow-sm"
                                    placeholder="{{ trans('client.branch_name') }}">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.city') }} *
                            </label>
                            <select name="city_id" id="city_id_select" required onchange="updateDistricts()"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800 shadow-sm">
                                <option value="">{{ trans('client.select_city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- District & Default -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.district') }} *
                            </label>
                            <select name="district_id" id="district_id_select" required
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800 shadow-sm">
                                <option value="">{{ trans('client.select_district') }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.is_default') }}
                            </label>
                            <select name="is_default" id="is_default_select"
                                class="select-premium w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-bold text-gray-800 shadow-sm">
                                <option value="0">{{ trans('client.no') }}</option>
                                <option value="1">{{ trans('client.yes') }}</option>
                            </select>
                        </div>
                    </div>


                    <!-- Full Address -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                            {{ trans('client.branch_address') }} *
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute top-4 right-0 pr-4 pointer-events-none text-gray-400 group-focus-within:text-[#1c75bc] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <textarea name="location" id="location_input" required rows="3"
                                class="w-full pr-12 p-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#1c75bc] focus:bg-white transition-all font-medium text-gray-800 shadow-sm resize-none text-sm"
                                placeholder="{{ trans('client.branch_placeholder') }}"></textarea>
                        </div>
                    </div>

                    <!-- Coordinates -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50/30 p-6 rounded-[24px] border border-blue-100/30">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-[#1c75bc] uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.lat') }} *
                            </label>
                            <input type="text" name="lat" id="lat_input" required
                                class="w-full p-4 bg-white border border-blue-50 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-mono text-gray-800 text-sm shadow-sm"
                                placeholder="24.7136">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-[#1c75bc] uppercase tracking-widest px-1 block mb-1">
                                {{ trans('client.lng') }} *
                            </label>
                            <input type="text" name="lng" id="lng_input" required
                                class="w-full p-4 bg-white border border-blue-50 rounded-xl outline-none focus:border-[#1c75bc] transition-all font-mono text-gray-800 text-sm shadow-sm"
                                placeholder="46.6753">
                        </div>
                        <div class="md:col-span-2 text-[11px] text-gray-400 font-medium px-1 italic">
                            {{ trans('client.branch_coordinates_help') }}
                        </div>
                    </div>

                    <!-- OpenStreetMap Container -->
                    <div class="space-y-4">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1 block mb-1">
                            {{ trans('client.branch_map_search_help') }}
                        </label>

                        <div id="branch-map" class="shadow-inner active overflow-hidden">
                            <!-- Leaflet Map -->
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/50 flex justify-end gap-4 dir-dependent-flex">
                    <button type="button"
                        class="px-8 py-4 bg-white border border-gray-200 text-gray-600 font-bold rounded-2xl hover:bg-gray-100 transition-all shadow-sm"
                        onclick="closeBranchModal()">
                        {{ trans('client.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-12 py-4 bg-gradient-to-r from-[#1c75bc] to-[#155a91] text-white font-black tracking-wide rounded-2xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:-translate-y-0.5">
                        {{ trans('client.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const citiesData = @json($cities);

    function updateDistricts(selectedDistrictId = null) {
        const cityId = document.getElementById('city_id_select').value;
        const districtSelect = document.getElementById('district_id_select');
        districtSelect.innerHTML = `<option value="">{{ trans('client.select_district') }}</option>`;

        if (cityId) {
            const city = citiesData.find(c => c.id == cityId);
            if (city && city.districts) {
                city.districts.forEach(d => {
                    const option = document.createElement('option');
                    option.value = d.id;
                    option.textContent = d.name;
                    if (selectedDistrictId && d.id == selectedDistrictId) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });
            }
        }
    }

    let branchMap = null;
    let branchMarker = null;
    const defaultLat = 24.7136; // Riyadh default
    const defaultLng = 46.6753;

    function reverseGeocode(lat, lng) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=ar`;

        fetch(url, {
            headers: {
                'Accept-Language': 'ar',
                'User-Agent': 'B2B-Branch-Manager'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('location_input').value = data.display_name;
                }
            })
            .catch(error => {
                console.error('Error in reverse geocoding:', error);
            });
    }

    function initMap(lat = defaultLat, lng = defaultLng) {
        if (typeof L === 'undefined') {
            console.error("Leaflet is not loaded yet");
            return;
        }

        if (!branchMap) {
            branchMap = L.map('branch-map', {
                scrollWheelZoom: false,
                attributionControl: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                subdomains: ['a', 'b', 'c']
            }).addTo(branchMap);

            // Add In-Map Search Control
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: "ابحث عن موقع...",
                errorMessage: "لم يتم العثور على نتائج"
            })
                .on('markgeocode', function (e) {
                    const latlng = e.geocode.center;
                    branchMap.setView(latlng, 17);
                    branchMarker.setLatLng(latlng);
                    document.getElementById('lat_input').value = latlng.lat.toFixed(6);
                    document.getElementById('lng_input').value = latlng.lng.toFixed(6);

                    // Update location address
                    document.getElementById('location_input').value = e.geocode.name;
                })
                .addTo(branchMap);

            branchMarker = L.marker([lat, lng], { draggable: true }).addTo(branchMap);

            branchMarker.on('dragend', function (event) {
                const position = branchMarker.getLatLng();
                document.getElementById('lat_input').value = position.lat.toFixed(6);
                document.getElementById('lng_input').value = position.lng.toFixed(6);

                // Reverse geocode on drag end
                reverseGeocode(position.lat, position.lng);
            });

            branchMap.on('click', function (e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                branchMarker.setLatLng([lat, lng]);
                document.getElementById('lat_input').value = lat.toFixed(6);
                document.getElementById('lng_input').value = lng.toFixed(6);

                // Reverse geocode on map click
                reverseGeocode(lat, lng);
            });
        } else {
            branchMap.setView([lat, lng], 15);
            branchMarker.setLatLng([lat, lng]);
        }

        // Recalculate size after display - higher timeout for reliability
        setTimeout(() => {
            if (branchMap) {
                branchMap.invalidateSize(true);
            }
        }, 800);
    }

    // Sync inputs back to map
    ['lat_input', 'lng_input'].forEach(id => {
        document.getElementById(id).addEventListener('change', function () {
            const lat = parseFloat(document.getElementById('lat_input').value) || defaultLat;
            const lng = parseFloat(document.getElementById('lng_input').value) || defaultLng;
            if (branchMarker) {
                branchMarker.setLatLng([lat, lng]);
                branchMap.setView([lat, lng], 13);
            }
        });
    });

    function openBranchModal(branch = null) {
        const modal = document.getElementById('branch-modal');
        const form = document.getElementById('branch-form');
        const title = document.getElementById('modal-title');
        const methodContainer = document.getElementById('method-container');

        let lat = defaultLat;
        let lng = defaultLng;

        if (branch) {
            title.innerHTML = `<span class="bg-clip-text text-transparent bg-gradient-to-r from-[#1c75bc] to-[#155a91]">{{ trans('client.edit_branch') }}</span>`;
            form.action = "{{ route('client.branches.index') }}/" + branch.id;
            methodContainer.innerHTML = `<input type="hidden" name="_method" value="POST">`;
            document.getElementById('branch_name_input').value = branch.name;
            document.getElementById('city_id_select').value = branch.city_id;
            updateDistricts(branch.district_id);
            document.getElementById('is_default_select').value = branch.is_default;
            document.getElementById('location_input').value = branch.location;

            lat = parseFloat(branch.lat) || defaultLat;
            lng = parseFloat(branch.lng) || defaultLng;

            document.getElementById('lat_input').value = lat;
            document.getElementById('lng_input').value = lng;
        } else {
            title.innerHTML = `<span class="bg-clip-text text-transparent bg-gradient-to-r from-[#1c75bc] to-[#155a91]">{{ trans('client.add_branch') }}</span>`;
            form.action = "{{ route('client.branches.store') }}";
            methodContainer.innerHTML = "";
            form.reset();
            document.getElementById('district_id_select').innerHTML = `<option value="">{{ trans('client.select_district') }}</option>`;
            document.getElementById('lat_input').value = lat;
            document.getElementById('lng_input').value = lng;
        }

        modal.classList.remove('hidden');
        $('body').addClass('overflow-hidden');

        // Give browser time to show modal then init map
        // The setTimeout for invalidateSize inside initMap already handles timing for map rendering.
        initMap(lat, lng);
    }

    function closeBranchModal() {
        document.getElementById('branch-modal').classList.add('hidden');
        $('body').removeClass('overflow-hidden');
    }
</script>
@endpush