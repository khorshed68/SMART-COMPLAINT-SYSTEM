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

            @if($complaint->status === 'Resolved')
                <div class="card mb-4 mt-4" id="satisfaction-rating-card">
                    <div class="card-header font-weight-bold">
                        <i class="fas fa-smile mr-2 text-warning"></i> Customer Satisfaction Feedback
                    </div>
                    <div class="card-body">
                        @if($complaint->rating === null)
                            <!-- Rating input form -->
                            <form id="rating-feedback-form">
                                <p class="mb-3 text-muted" style="font-size: 0.9rem;">
                                    This complaint has been resolved. Please take a moment to rate our service and let us know your feedback!
                                </p>
                                
                                <div class="rating-stars-select mb-4 text-center" style="user-select: none;">
                                    <input type="hidden" name="rating" id="rating-input-value" value="0">
                                    <span class="star-btn" data-star="1" style="cursor: pointer; margin: 0 5px; color: #cbd5e1; transition: color 0.15s ease;"><i class="far fa-star fa-2x"></i></span>
                                    <span class="star-btn" data-star="2" style="cursor: pointer; margin: 0 5px; color: #cbd5e1; transition: color 0.15s ease;"><i class="far fa-star fa-2x"></i></span>
                                    <span class="star-btn" data-star="3" style="cursor: pointer; margin: 0 5px; color: #cbd5e1; transition: color 0.15s ease;"><i class="far fa-star fa-2x"></i></span>
                                    <span class="star-btn" data-star="4" style="cursor: pointer; margin: 0 5px; color: #cbd5e1; transition: color 0.15s ease;"><i class="far fa-star fa-2x"></i></span>
                                    <span class="star-btn" data-star="5" style="cursor: pointer; margin: 0 5px; color: #cbd5e1; transition: color 0.15s ease;"><i class="far fa-star fa-2x"></i></span>
                                    <div class="rating-label-display mt-2 font-weight-bold text-muted" id="rating-label" style="font-size: 0.9rem;">Select a rating</div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="feedback-text" class="form-label font-weight-bold text-muted" style="font-size: 0.85rem;">Feedback & Comments (Optional)</label>
                                    <textarea name="feedback" id="feedback-text" class="form-control" rows="3" placeholder="Tell us about your experience..." style="border-radius: 8px; font-size: 0.9rem;"></textarea>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-secondary px-4 py-2" id="submit-feedback-btn" disabled style="border-radius: 8px; font-weight: 600;">
                                        <i class="fas fa-paper-plane mr-1"></i> Submit Feedback
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Rating review display -->
                            <div class="text-center py-2">
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $complaint->rating)
                                            <i class="fas fa-star text-warning fa-2x"></i>
                                        @else
                                            <i class="far fa-star text-warning fa-2x"></i>
                                        @endif
                                    @endfor
                                </div>
                                <h4 class="font-weight-bold mb-3" style="color: var(--dark);">
                                    Rating: {{ $complaint->rating }} / 5
                                </h4>
                                @if($complaint->feedback)
                                    <blockquote class="p-3 bg-light border italic" style="border-radius: 8px; font-size: 0.92rem; color: #4a5568; margin: 15px auto; max-width: 600px; border-left: 4px solid var(--primary) !important;">
                                        "{{ $complaint->feedback }}"
                                    </blockquote>
                                @else
                                    <p class="text-muted italic mb-0" style="font-size: 0.9rem;">No comments provided.</p>
                                @endif
                                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                                    Rated on {{ $complaint->rated_at ? $complaint->rated_at->format('Y-m-d H:i') : '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
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
