@extends('layouts.admin')

@section('title', 'Admin Complaint Panel - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in" style="max-width: 1000px; margin: 0 auto;">
    <!-- Breadcrumb back link -->
    <div class="mb-4">
        <a href="{{ route('admin.complaints') }}" class="text-primary font-weight-bold" style="text-decoration: none;">
            <i class="fas fa-chevron-left mr-1"></i> Back to Complaints List
        </a>
    </div>

    <div class="row">
        <!-- Main details card -->
        <div class="col-md-7">
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
                            <span class="text-muted font-weight-bold d-block mb-1">Submitted By</span>
                            <span class="font-weight-bold">{{ $complaint->user->name }} ({{ $complaint->user->email }})</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted font-weight-bold d-block mb-1">Category</span>
                            <span class="font-weight-bold"><i class="fas {{ $complaint->category->icon ?? 'fa-tag' }} mr-1"></i> {{ $complaint->category->name ?? 'Other' }}</span>
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

        <!-- Admin Action Panel -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">Admin Action Panel</div>
                <div class="card-body">
                    <!-- Status update form -->
                    <div class="form-group mb-4">
                        <label>Update Status</label>
                        <select id="action-status-select" class="form-select" onchange="promptStatusChange(this.value)">
                            <option value="Pending" {{ $complaint->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $complaint->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $complaint->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="Rejected" {{ $complaint->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Assignee form -->
                    <div class="form-group mb-4">
                        <label>Assign Administrator</label>
                        <select class="form-select" onchange="assignComplaint({{ $complaint->id }}, this.value)">
                            <option value="">Unassigned</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $complaint->assigned_to === $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority form -->
                    <div class="form-group mb-4">
                        <label>Priority level</label>
                        <select class="form-select" onchange="changePriority({{ $complaint->id }}, this.value)">
                            <option value="Low" {{ $complaint->priority === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $complaint->priority === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $complaint->priority === 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #edf2f7; margin: 25px 0;">

                    <!-- Post Comment Form -->
                    <div class="form-group">
                        <label>Post Comment / Message</label>
                        <textarea id="action-comment-input" class="form-control" rows="4" placeholder="Write a message or resolution notes to post on the complaint's timeline..."></textarea>
                        <button type="button" class="btn btn-primary w-100 mt-2" onclick="submitTimelineComment()">Post Comment</button>
                    </div>

                    <!-- Delete button -->
                    <button type="button" class="btn btn-outline-danger w-100 mt-4" onclick="deleteAndRedirect()"><i class="fas fa-trash"></i> Delete Complaint</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function promptStatusChange(status) {
        const modal = new Modal('status-comment-modal');
        modal.setTitle('Update Status Notes');
        modal.setBody(`
            <div class="form-group">
                <label>Add comment/explanation for status update (optional)</label>
                <textarea id="status-comment-text" class="form-control" rows="3" placeholder="Provide details about why the status is changing..."></textarea>
            </div>
        `);
        
        const saveBtn = document.createElement('button');
        saveBtn.className = 'btn btn-primary';
        saveBtn.textContent = 'Save Status';
        saveBtn.onclick = () => {
            const comment = document.getElementById('status-comment-text').value;
            updateComplaintStatus({{ $complaint->id }}, status, comment);
            modal.hide();
        };

        modal.setFooter('');
        modal.footerEl.appendChild(saveBtn);
        modal.show();
    }

    function submitTimelineComment() {
        const comment = document.getElementById('action-comment-input').value;
        if (!comment.trim()) {
            Toast.show('Please write a comment.', 'error');
            return;
        }
        addComment({{ $complaint->id }}, comment);
    }

    function deleteAndRedirect() {
        ConfirmDialog.show('Delete Complaint', 'Are you sure you want to delete this complaint? This is irreversible.', function() {
            $.ajax({
                url: '/api/admin/complaints/{{ $complaint->id }}',
                method: 'DELETE',
                success: function() {
                    Toast.show('Complaint deleted successfully.', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route('admin.complaints') }}';
                    }, 1000);
                }
            });
        });
    }

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
        loadTimeline();
        setInterval(loadTimeline, 30000);
    });
</script>
@endsection
