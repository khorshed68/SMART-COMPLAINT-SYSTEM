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
    });
</script>
@endsection
