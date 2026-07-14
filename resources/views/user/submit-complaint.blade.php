@extends('layouts.app')

@section('title', 'Submit Complaint - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 280px;
        width: 100%;
        border-radius: 12px;
        border: 1px solid var(--border-color, #cbd5e1);
        margin-top: 10px;
        z-index: 10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="container fade-in-up" style="max-width: 850px; margin-top: 20px; margin-bottom: 50px;">
    <div class="card border-0 shadow-sm rounded-4" style="overflow: hidden;">
        <div class="card-header bg-transparent border-0 py-4"><h4 class="font-weight-bold mb-0">Submit a New Complaint</h4></div>
        <div class="card-body p-4">
            <form onsubmit="submitComplaint(event)" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label class="font-weight-bold mb-2" for="complaint-title">Complaint Title</label>
                    <input type="text" id="complaint-title" name="title" class="form-control" placeholder="Brief summary of the issue (min 5 characters)" required minlength="5" maxlength="150" style="border-radius: 10px; padding: 12px 16px;">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label class="font-weight-bold mb-2" for="complaint-category">Category</label>
                            <select id="complaint-category" name="category_id" class="form-select" required style="border-radius: 10px; padding: 12px 16px;">
                                <!-- Loaded via AJAX -->
                            </select>
                            <div id="category-suggestion-box" class="mt-2 d-none" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; cursor: pointer; animation: fadeIn 0.3s ease; background-color: rgba(52, 152, 219, 0.08); padding: 8px 12px; border-radius: 6px; border: 1px dashed rgba(52, 152, 219, 0.3);">
                                <i class="fas fa-lightbulb text-warning"></i> Suggested: <span id="suggested-category-name" style="text-decoration: underline;">Internet</span> (Click to apply)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label class="font-weight-bold mb-2">Priority Level</label>
                            <div class="d-flex align-items-center" style="gap: 20px; height: 48px;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="priority" value="Low" style="accent-color: var(--secondary);"> Low
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="priority" value="Medium" checked style="accent-color: var(--warning);"> Medium
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="priority" value="High" style="accent-color: var(--danger);"> High
                                </label>
                            </div>
                            <div id="priority-suggestion-box" class="mt-1 d-none" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; cursor: pointer; animation: fadeIn 0.3s ease; background-color: rgba(52, 152, 219, 0.08); padding: 8px 12px; border-radius: 6px; border: 1px dashed rgba(52, 152, 219, 0.3); margin-top: -5px;">
                                <i class="fas fa-exclamation-circle text-warning"></i> Recommended: <span id="suggested-priority-name" style="text-decoration: underline;">High</span> (Click to apply)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outage/Maintenance Alert Container -->
                <div id="active-outage-alert" class="alert alert-warning border-0 shadow-sm d-none mb-3 fade-in" style="background-color: rgba(243, 156, 18, 0.08); border-left: 4px solid var(--warning) !important; border-radius: 8px;">
                    <div class="d-flex gap-3">
                        <div class="text-warning" style="font-size: 1.5rem;"><i class="fas fa-exclamation-triangle animate-pulse"></i></div>
                        <div style="flex-grow: 1;">
                            <h5 class="font-weight-bold mb-1" style="color: var(--dark); font-size: 0.95rem;">Active Outage / Maintenance Alert</h5>
                            <p id="outage-alert-msg" class="mb-2" style="font-size: 0.85rem; color: #4a5568; line-height: 1.4;"></p>
                            <div class="d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                <span class="text-muted"><i class="far fa-clock mr-1"></i> Est. Resolution: <strong id="outage-alert-eta"></strong></span>
                                <button type="button" class="btn btn-sm btn-dark font-weight-bold py-1 px-2.5" onclick="$('#active-outage-alert').addClass('d-none');" style="font-size: 0.72rem; border-radius: 4px;">Dismiss & Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Sector with KUET Campus Pins -->
                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6" style="padding: 0 10px;">
                            <label class="font-weight-bold mb-2" for="complaint-location">Location / Area</label>
                            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                                <input type="text" id="complaint-location" name="location" class="form-control" placeholder="e.g. Lalan Shah Hall Room 203" required style="flex-grow: 1; border-radius: 10px; padding: 12px 16px;">
                                <button type="button" class="btn btn-primary animate-hover" id="btn-search-location" style="flex-shrink: 0; padding: 12px 16px; font-weight: 600; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;" title="Search location name">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <button type="button" class="btn btn-success animate-hover" id="btn-current-location" style="flex-shrink: 0; padding: 12px 16px; font-weight: 600; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; background-color: var(--secondary); border-color: var(--secondary); color: white;" title="Use my current location">
                                    <i class="fas fa-location-arrow"></i> Locate Me
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding: 0 10px;">
                            <label class="font-weight-bold mb-2" for="kuet-quick-pin">KUET Campus Quick Pins</label>
                            <select id="kuet-quick-pin" class="form-select" style="border-radius: 10px; padding: 12px 16px; margin-bottom: 10px;">
                                <option value="">Select a Campus Landmark...</option>
                                <optgroup label="Residential Halls">
                                    <option value="22.8985,89.5042">Lalan Shah Hall</option>
                                    <option value="22.8995,89.5045">Amar Ekushey Hall</option>
                                    <option value="22.8975,89.5038">Bangabandhu Sheikh Mujibur Rahman Hall</option>
                                    <option value="22.9018,89.5042">Rokeya Hall (Female)</option>
                                    <option value="22.9025,89.5035">Dr. M. A. Rashid Hall</option>
                                    <option value="22.9030,89.5020">Fazlul Huq Hall</option>
                                    <option value="22.9038,89.5015">Khan Jahan Ali Hall</option>
                                </optgroup>
                                <optgroup label="Academic & Administrative Buildings">
                                    <option value="22.9006,89.5018">Administration Building</option>
                                    <option value="22.9015,89.5015">Department of CSE</option>
                                    <option value="22.9010,89.5017">Department of EEE</option>
                                    <option value="22.9008,89.5012">Mechanical Engineering Building</option>
                                    <option value="22.8998,89.5015">Civil Engineering Building</option>
                                    <option value="22.9008,89.5024">Central Library</option>
                                    <option value="22.9004,89.5027">Student Cafeteria</option>
                                    <option value="22.9009,89.5010">Auditorium</option>
                                </optgroup>
                                <optgroup label="Campus Landmarks">
                                    <option value="22.8992,89.5005">KUET Main Gate</option>
                                    <option value="22.9003,89.5032">Central Mosque</option>
                                    <option value="22.8990,89.5025">KUET Playground</option>
                                    <option value="22.9015,89.5028">KUET Medical Center</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block" style="margin-bottom: 8px;"><i class="fas fa-map-marked-alt text-info mr-1"></i> Or click/drag the pin on the map below to pinpoint the exact location:</small>
                    <div id="map"></div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold mb-2" for="complaint-desc">Detailed Description</label>
                    <textarea id="complaint-desc" name="description" class="form-control" rows="5" placeholder="Provide a thorough explanation of the issue so we can help resolve it efficiently..." required minlength="10" style="border-radius: 10px; padding: 12px 16px;"></textarea>
                </div>

                <!-- Drag & Drop File Upload Zone -->
                <div class="form-group mb-4">
                    <label class="font-weight-bold mb-2">Attachment (Optional)</label>
                    <div class="upload-zone" id="file-upload-zone" style="border-radius: 12px;">
                        <input type="file" id="file-attachment" name="attachment" style="display: none;">
                        <i class="fas fa-cloud-upload-alt text-primary mb-2" style="font-size: 2.2rem;"></i>
                        <p class="font-weight-bold mb-1">Drag and drop file here, or click to upload</p>
                        <p class="text-muted mb-0" style="font-size: 0.78rem;">Allowed formats: jpg, jpeg, png, gif, pdf, doc, docx (Max: 5MB)</p>
                    </div>
                    <div id="file-preview-container"></div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-dark" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(document).ready(function() {
        // Load categories dropdown
        loadCategories('complaint-category');

        // Initialize drag & drop uploader
        new FileUploadComponent('file-upload-zone', 'file-attachment', 'file-preview-container');

        // Check for active maintenance when category changes
        $('#complaint-category').change(function() {
            const catId = $(this).val();
            if (!catId) {
                $('#active-outage-alert').addClass('d-none');
                return;
            }

            $.get('/api/announcements/active', { category_id: catId }, function(data) {
                // Use first returned announcement
                const outage = data.length > 0 ? data[0] : null;
                
                if (outage) {
                    $('#outage-alert-msg').html(`We are currently experiencing an active issue/maintenance: <strong>"${outage.title}"</strong>. <br><span class="text-muted" style="font-size:0.8rem; display:block; margin-top:4px;">Details: ${outage.content}</span>`);
                    
                    const endTime = new Date(outage.end_time);
                    $('#outage-alert-eta').text(endTime.toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }));
                    $('#active-outage-alert').removeClass('d-none');
                } else {
                    $('#active-outage-alert').addClass('d-none');
                }
            });
        });

        // Initialize Leaflet Map centered on default campus coordinates (KUET campus center)
        const defaultLat = 22.9006; 
        const defaultLon = 89.5024;
        
        const map = L.map('map').setView([defaultLat, defaultLon], 16.5);
        
        // Load OpenStreetMap tiles asynchronously
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add a draggable marker
        let marker = L.marker([defaultLat, defaultLon], { draggable: true }).addTo(map);
        
        // Reverse-geocodes coordinate using OpenStreetMap Nominatim API
        function updateLocationFromMap(lat, lon) {
            // Set coordinate fallback instantly
            $('#complaint-location').val(`Lat: ${lat.toFixed(5)}, Lon: ${lon.toFixed(5)}`);
            
            // Query OSM Nominatim reverse API asynchronously (AJAX/Fetch)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        const addr = data.address;
                        const shortAddr = [
                            addr.suburb || addr.neighbourhood || addr.residential || '',
                            addr.road || '',
                            addr.city || addr.town || ''
                        ].filter(Boolean).join(', ');
                        
                        if (shortAddr) {
                            $('#complaint-location').val(`${shortAddr} (Lat: ${lat.toFixed(4)}, Lon: ${lon.toFixed(4)})`);
                        } else {
                            $('#complaint-location').val(data.display_name);
                        }
                    }
                })
                .catch(err => {
                    // Fail silently, fallback is already in place
                });
        }
        
        // Map click handler
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;
            marker.setLatLng([lat, lon]);
            updateLocationFromMap(lat, lon);
            // Reset quick pin selector if custom clicked
            $('#kuet-quick-pin').val('');
        });
        
        // Marker drag handler
        marker.on('dragend', function() {
            const lat = marker.getLatLng().lat;
            const lon = marker.getLatLng().lng;
            updateLocationFromMap(lat, lon);
            // Reset quick pin selector if custom dragged
            $('#kuet-quick-pin').val('');
        });

        // KUET Quick Pin Change Handler
        $('#kuet-quick-pin').change(function() {
            const val = $(this).val();
            if (!val) return;

            const coords = val.split(',');
            const lat = parseFloat(coords[0]);
            const lon = parseFloat(coords[1]);
            const name = $(this).find('option:selected').text();

            // Move marker and fly map to coordinates
            marker.setLatLng([lat, lon]);
            map.flyTo([lat, lon], 17);

            // Update input field
            $('#complaint-location').val(`${name}, KUET (Lat: ${lat.toFixed(4)}, Lon: ${lon.toFixed(4)})`);
            Toast.show(`Pinned ${name} on map!`, 'success');
        });

        // Current Geolocation Handler
        $('#btn-current-location').click(function() {
            if (!navigator.geolocation) {
                Toast.show('Geolocation is not supported by your browser.', 'error');
                return;
            }

            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Locating...');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Move marker and fly map to coordinates
                    marker.setLatLng([lat, lon]);
                    map.flyTo([lat, lon], 17);

                    // Update input field and reverse geocode
                    updateLocationFromMap(lat, lon);
                    
                    // Reset quick pin selection
                    $('#kuet-quick-pin').val('');

                    btn.prop('disabled', false).html(originalHtml);
                    Toast.show('Current location pinned!', 'success');
                },
                function(error) {
                    btn.prop('disabled', false).html(originalHtml);
                    let errorMsg = 'Failed to retrieve your location.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMsg = 'Location permission denied by browser settings.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errorMsg = 'Location information is unavailable.';
                    } else if (error.code === error.TIMEOUT) {
                        errorMsg = 'Location request timed out.';
                    }
                    Toast.show(errorMsg, 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });

        // Intercept Enter key inside the location input to search instead of submitting the form
        $('#complaint-location').keydown(function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = $(this).val().trim();
                if (query.length > 2) {
                    searchLocationFromInput(query);
                }
            }
        });

        // Search button click handler
        $('#btn-search-location').click(function() {
            const query = $('#complaint-location').val().trim();
            if (query.length > 2) {
                searchLocationFromInput(query);
            } else {
                Toast.show('Please type a location name to search.', 'info');
            }
        });

        // Forward geocoding function (Search location from name)
        function searchLocationFromInput(query) {
            // Query OSM Nominatim search API asynchronously (AJAX/Fetch)
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        
                        // Move marker and fly map to coordinates
                        marker.setLatLng([lat, lon]);
                        map.flyTo([lat, lon], 16);
                        
                        // Update input field value with the clean geocoded display name
                        $('#complaint-location').val(data[0].display_name);
                        Toast.show('Location pinned on map!', 'success');
                        
                        // Reset quick pin selection
                        $('#kuet-quick-pin').val('');
                    } else {
                        Toast.show('Location not found. Try pinning manually on the map.', 'warning');
                    }
                })
                .catch(err => {
                    Toast.show('Could not search location. Pin manually.', 'error');
                });
        }
    });
</script>
@endsection
