@extends('layouts.admin')

@section('title', 'Manage Categories - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-folder-open"></i> Categories Management</h1>
        <button onclick="editCategoryModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Category</button>
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
