@extends('layouts.app')

@section('title', 'Complaint Detail - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .timeline-item { position: relative; padding-left: 30px; margin-bottom: 20px; border-left: 2px solid var(--border-color); }
        .timeline-dot { position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background: var(--primary); }
        .timeline-content { background: var(--card-bg); padding: 12px 15px; border-radius: 8px; border: 1px solid var(--border-color); }
        .timeline-header { display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px; }
        .timeline-body { font-size: 0.88rem; line-height: 1.5; color: var(--text-color); }
    </style>
@endsection

@section('content')
<div class="container fade-in" style="max-width: 1000px;">
    <!-- Breadcrumb back link -->
    <div class="mb-4">
        <a href="{{ route('staff.dashboard') }}" class="text-primary font-weight-bold" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-chevron-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <!-- Main details card -->
        <div class="col-md-7">
            <div class="card mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <span class="font-weight-bold">Complaint Details</span>
                    <span class="text-muted font-weight-bold" style="font-size: 0.85rem;">#{{ $complaint->id }}</span>
                </div>
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3" style="font-size: 1.4rem; color: var(--text-color);">{{ $complaint->title }}</h2>
                    
                    <div style="font-size: 0.95rem; line-height: 1.6; color: var(--text-color); margin-bottom: 25px; opacity: 0.9;">
                        {!! nl2br(e($complaint->description)) !!}
                    </div>

                    <!-- Meta specifications -->
                    <div class="row g-3" style="background: rgba(255, 255, 255, 0.02); border-radius: 8px; padding: 15px; border: 1px solid var(--border-color); font-size: 0.85rem;">
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Submitted Date</span>
                            <span class="font-weight-bold" style="color: var(--text-color);">{{ $complaint->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Location / Area</span>
                            <span class="font-weight-bold" style="color: var(--text-color);">{{ $complaint->location ?? 'Not Specified' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Submitted By</span>
                            <span class="font-weight-bold" style="color: var(--text-color);">{{ $complaint->user->name }} ({{ $complaint->user->email }})</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Category</span>
                            <span class="font-weight-bold" style="color: var(--text-color);">
                                <i class="fas {{ $complaint->category->icon ?? 'fa-tag' }} mr-1"></i> {{ $complaint->category->name ?? 'Other' }}
                            </span>
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($complaint->attachment)
                        <div class="mt-4">
                            <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.85rem;">Attachment</span>
                            <div class="d-flex align-items-center p-3" style="background: rgba(255, 255, 255, 0.01); border-radius: 8px; border: 1.5px solid var(--border-color); width: 100%; max-width: 320px;">
                                <i class="far fa-file-alt text-primary mr-3" style="font-size: 1.6rem;"></i>
                                <div style="overflow: hidden; flex-grow: 1;">
                                    <div class="font-weight-bold" style="font-size: 0.82rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-color);">{{ basename($complaint->attachment) }}</div>
                                    <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 0.78rem; text-decoration: none;">Download File</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Map Location Card -->
            <div class="card mb-4" id="map-section-card" style="display: none; background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header font-weight-bold" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <i class="fas fa-map-marked-alt mr-1 text-primary"></i> Target Map Location
                </div>
                <div class="card-body p-2">
                    <div id="complaint-map" style="height: 250px; border-radius: 8px; border: 1px solid var(--border-color);"></div>
                </div>
            </div>

            <!-- Timeline Updates -->
            <div class="card mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header font-weight-bold" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    Activity Timeline
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div class="timeline" id="complaint-timeline-container" style="border-left: none;">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-md-5">
            <div class="card mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header font-weight-bold" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    Staff Action Panel
                </div>
                <div class="card-body">
                    <form id="staff-action-form" onsubmit="submitStatusChange(event)">
                        <!-- Status Update -->
                        <div class="form-group mb-4">
                            <label class="form-label font-weight-bold" style="font-size: 0.9rem; color: var(--text-color);">Update Status</label>
                            <select id="action-status-select" class="form-select" required>
                                <option value="In Progress" {{ $complaint->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved" {{ $complaint->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <!-- Notes / Comment -->
                        <div class="form-group mb-4">
                            <label class="form-label font-weight-bold" style="font-size: 0.9rem; color: var(--text-color);">Add Comment / Resolution Notes</label>
                            <textarea id="action-comment-input" class="form-control" rows="5" placeholder="Provide details about what work was done, active updates, or final resolution notes..." required style="border-radius: 8px; font-size: 0.9rem;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 font-weight-bold animate-hover" style="border-radius: 20px;">
                            <i class="fas fa-save mr-1"></i> Update Progress
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header font-weight-bold" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    Status Panel
                </div>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.8rem;">Current Status</span>
                        <x-status-badge :status="$complaint->status" />
                    </div>
                    <div>
                        <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.8rem;">Priority Level</span>
                        <x-priority-badge :priority="$complaint->priority" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function loadTimeline() {
            const container = document.getElementById('complaint-timeline-container');
            if (!container) return;

            $.get('/api/complaints/{{ $complaint->id }}/updates', function(updates) {
                container.innerHTML = '';
                if (updates.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center mb-0" style="font-size: 0.85rem;">No timeline updates available.</p>';
                    return;
                }

                updates.forEach(item => {
                    const timelineItem = document.createElement('div');
                    timelineItem.className = 'timeline-item';
                    
                    let dotColor = 'var(--primary)';
                    if (item.update_type === 'status_change') {
                        if (item.new_status === 'Resolved') dotColor = '#10b981';
                        else if (item.new_status === 'Rejected') dotColor = '#ef4444';
                        else if (item.new_status === 'In Progress') dotColor = '#8b5cf6';
                    } else if (item.update_type === 'assignment') {
                        dotColor = '#f59e0b';
                    }

                    timelineItem.innerHTML = `
                        <div class="timeline-dot" style="background-color: ${dotColor}"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="font-weight-bold">${item.updater ? item.updater.name : 'System'}</span>
                                <span>${timeAgo(item.created_at)}</span>
                            </div>
                            <div class="timeline-body">
                                ${item.comment ? item.comment : `<span class="text-muted">Updated complaint state to: ${item.new_status}</span>`}
                            </div>
                        </div>
                    `;
                    container.appendChild(timelineItem);
                });
            });
        }

        function submitStatusChange(e) {
            e.preventDefault();
            const status = document.getElementById('action-status-select').value;
            const comment = document.getElementById('action-comment-input').value.trim();

            if (!comment) {
                Toast.show('Please provide a comment detailing the update.', 'error');
                return;
            }

            const btn = $(e.target).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

            $.post('/api/staff/complaints/{{ $complaint->id }}/status', {
                status: status,
                comment: comment
            }, function(response) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Progress');
                if (response.success) {
                    Toast.show(response.message, 'success');
                    document.getElementById('action-comment-input').value = '';
                    loadTimeline();
                    // Reload page after a delay to update page states
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Toast.show('Failed to update status.', 'error');
                }
            }).fail(function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Progress');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                Toast.show(msg, 'error');
            });
        }

        $(document).ready(function() {
            loadTimeline();
            setInterval(loadTimeline, 30000);

            // Parse location for coordinates and render Leaflet Map
            const locationText = "{{ $complaint->location }}";
            // Matches formats like "Lat: 23.81234, Lon: 90.41234"
            const regex = /Lat:\s*([0-9.-]+),\s*L(?:on|ng):\s*([0-9.-]+)/i;
            const match = locationText.match(regex);
            
            if (match) {
                const lat = parseFloat(match[1]);
                const lng = parseFloat(match[2]);
                
                $('#map-section-card').show();
                
                const map = L.map('complaint-map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<strong>Complaint Location</strong><br>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`)
                    .openPopup();
            }
        });
    </script>
@endsection
