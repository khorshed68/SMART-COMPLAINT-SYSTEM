@extends('layouts.app')

@section('title', 'Submit Complaint - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in" style="max-width: 800px;">
    <div class="card slide-up">
        <div class="card-header">
            <span>Submit a New Complaint</span>
        </div>
        <div class="card-body">
            <form onsubmit="submitComplaint(event)" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="complaint-title">Complaint Title</label>
                    <input type="text" id="complaint-title" name="title" class="form-control" placeholder="Brief summary of the issue (min 5 characters)" required minlength="5" maxlength="150">
                </div>

                <div class="row">
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label for="complaint-category">Category</label>
                            <select id="complaint-category" name="category_id" class="form-select" required>
                                <!-- Loaded via AJAX -->
                            </select>
                            <div id="category-suggestion-box" class="mt-2 d-none" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; cursor: pointer; animation: fadeIn 0.3s ease; background-color: rgba(52, 152, 219, 0.08); padding: 8px 12px; border-radius: 6px; border: 1px dashed rgba(52, 152, 219, 0.3);">
                                <i class="fas fa-lightbulb text-warning"></i> Suggested: <span id="suggested-category-name" style="text-decoration: underline;">Internet</span> (Click to apply)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label>Priority Level</label>
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

                <div class="form-group">
                    <label for="complaint-location">Location / Area</label>
                    <input type="text" id="complaint-location" name="location" class="form-control" placeholder="e.g. Hostel A Room 203, Central Library, CSE Seminar Room">
                </div>

                <div class="form-group">
                    <label for="complaint-desc">Detailed Description</label>
                    <textarea id="complaint-desc" name="description" class="form-control" rows="6" placeholder="Provide a thorough explanation of the issue so we can help resolve it efficiently..." required minlength="10"></textarea>
                </div>

                <!-- Drag & Drop File Upload Zone -->
                <div class="form-group">
                    <label>Attachment (Optional)</label>
                    <div class="upload-zone" id="file-upload-zone">
                        <input type="file" id="file-attachment" name="attachment" style="display: none;">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="font-weight-bold">Drag and drop file here, or click to upload</p>
                        <p class="text-muted mt-1" style="font-size: 0.78rem;">Allowed formats: jpg, jpeg, png, gif, pdf, doc, docx (Max: 5MB)</p>
                    </div>
                    <div id="file-preview-container"></div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-dark">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
    });
</script>
@endsection
