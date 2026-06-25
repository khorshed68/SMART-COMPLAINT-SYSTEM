@extends('layouts.app')

@section('title', 'Complaint Details - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in" style="max-width: 900px;">
    <!-- Breadcrumb back link -->
    <div class="mb-4">
        <a href="{{ route('complaints.index') }}" class="text-primary font-weight-bold" style="text-decoration: none;">
            <i class="fas fa-chevron-left mr-1"></i> Back to Complaints
        </a>
    </div>

    <div class="row">
        <!-- Main details card -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Complaint Details</span>
                    <span class="text-muted font-weight-bold" style="font-size: 0.85rem;">#{{ $complaint->id }}</span>
                </div>
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3" style="font-size: 1.4rem;">{{ $complaint->title }}</h2>
                    
                    <div style="font-size: 0.95rem; line-height: 1.6; color: #4a5568; margin-bottom: 25px;">
                        {!! nl2br(e($complaint->description)) !!}
                    </div>

                    <!-- Meta specifications -->
                    <div class="row" style="background: #f8f9fa; border-radius: 8px; padding: 15px; border: 1px solid #edf2f7; font-size: 0.85rem; gap: 15px 0;">
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Submitted Date</span>
                            <span class="font-weight-bold">{{ $complaint->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Location / Area</span>
                            <span class="font-weight-bold">{{ $complaint->location ?? 'Not Specified' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Category</span>
                            <span class="font-weight-bold"><i class="fas {{ $complaint->category->icon ?? 'fa-tag' }} mr-1"></i> {{ $complaint->category->name ?? 'Other' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Assigned Admin</span>
                            <span class="font-weight-bold">{{ $complaint->assignee->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($complaint->attachment)
                        <div class="mt-4">
                            <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.85rem;">Attachment</span>
                            <div class="d-flex align-items-center p-3" style="background: #fff; border-radius: 8px; border: 1.5px solid var(--border); width: 100%; max-width: 320px;">
                                <i class="far fa-file-alt text-primary mr-3" style="font-size: 1.6rem;"></i>
                                <div style="overflow: hidden; flex-grow: 1;">
                                    <div class="font-weight-bold" style="font-size: 0.82rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ basename($complaint->attachment) }}</div>
                                    <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 0.78rem; text-decoration: none;">Download File</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Panel (Status, Priority, timeline) -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Status Panel</div>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.8rem;">Current Status</span>
                        <x-status-badge :status="$complaint->status" />
                    </div>
                    <div>
                        <span class="text-muted font-weight-bold d-block mb-2" style="font-size: 0.8rem;">Priority Level</span>
                        <x-priority-badge :priority="$complaint->priority" />
                    </div>
                    
                    @if($complaint->status === 'Resolved' && $complaint->resolution_notes)
                        <hr style="border: 0; border-top: 1px solid #edf2f7; margin: 20px 0;">
                        <div class="text-left" style="font-size: 0.85rem;">
                            <span class="text-muted font-weight-bold d-block mb-2">Resolution Notes:</span>
                            <div class="p-3 bg-light border" style="border-radius: 6px;">{{ $complaint->resolution_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Timeline Updates -->
            <div class="card">
                <div class="card-header">Activity Timeline</div>
                <div class="card-body p-3" style="position: relative;">
                    <div class="timeline" id="complaint-timeline-container">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function loadTimeline() {
        const container = document.getElementById('complaint-timeline-container');
        if (!container) return;

        $.get('/api/complaints/{{ $complaint->id }}/updates', function(updates) {
            container.innerHTML = '';
            if (updates.length === 0) {
                container.innerHTML = '<p class="text-muted text-center" style="font-size: 0.85rem;">No timeline updates available.</p>';
                return;
            }

            updates.forEach(item => {
                const timelineItem = document.createElement('div');
                timelineItem.className = 'timeline-item';
                
                // Color dots depending on update type
                let dotColor = 'var(--primary)';
                if (item.update_type === 'status_change') {
                    if (item.new_status === 'Resolved') dotColor = 'var(--secondary)';
                    else if (item.new_status === 'Rejected') dotColor = 'var(--danger)';
                    else if (item.new_status === 'In Progress') dotColor = 'var(--primary)';
                } else if (item.update_type === 'assignment') {
                    dotColor = '#f39c12';
                }

                timelineItem.innerHTML = `
                    <div class="timeline-dot" style="background-color: ${dotColor}"></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <span class="font-weight-bold" style="color: var(--dark);">${item.updater ? item.updater.name : 'System'}</span>
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

    $(document).ready(function() {
        // Load timeline
        loadTimeline();

        // 30s auto-refresh
        setInterval(loadTimeline, 30000);
    });
</script>
@endsection
