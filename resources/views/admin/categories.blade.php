@extends('layouts.admin')

@section('title', 'Manage Categories - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in-up">
    <div class="dashboard-section-header mb-4 d-flex justify-content-between align-items-center">
        <h1 class="dashboard-section-title mb-0"><i class="fas fa-folder-open text-primary mr-2"></i> Categories Management</h1>
        <button onclick="editCategoryModal()" class="btn btn-primary font-weight-bold" style="border-radius: 8px; padding: 10px 20px;"><i class="fas fa-plus mr-2"></i> Add Category</button>
    </div>

    <!-- Category Grid -->
    <div class="row" id="categories-card-grid">
        <!-- Loaded via AJAX -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Load categories cards
        loadAdminCategories();
    });
</script>
@endsection
