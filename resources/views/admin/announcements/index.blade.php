@extends('layouts.admin')

@section('title', 'Announcements Management - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container-fluid fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 font-weight-bold mb-0">Announcements Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Schedule Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title font-weight-bold mb-0" style="color: var(--dark);">Schedule New Announcement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.announcements.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. WiFi maintenance in Library" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Content Detail</label>
                            <textarea name="content" class="form-control" rows="4" placeholder="Describe the maintenance, outage, or warning details..." required></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Affected Category (Optional)</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Affects All Categories --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">If selected, users submitting complaints under this category will receive a warning.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Start Time</label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">End Time</label>
                                <input type="datetime-local" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 mt-2 font-weight-bold">
                            <i class="fas fa-bullhorn mr-1"></i> Schedule Announcement
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History & Status Table -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title font-weight-bold mb-0" style="color: var(--dark);">Announcements Listing</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Affected Category</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($announcements->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center text-muted p-5">
                                            <i class="fas fa-bullhorn mb-3" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                            <p class="mb-0">No announcements scheduled yet.</p>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($announcements as $ann)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold" style="color: var(--dark);">{{ $ann->title }}</div>
                                                <small class="text-muted" style="display: block; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $ann->content }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($ann->category)
                                                    <span class="badge" style="background-color: {{ $ann->category->color ?? '#95a5a6' }};">
                                                        {{ $ann->category->name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">All Categories</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-size: 0.82rem;">
                                                    <strong>From:</strong> {{ $ann->start_time->format('M d, Y g:i A') }}
                                                </div>
                                                <div style="font-size: 0.82rem;">
                                                    <strong>To:</strong> {{ $ann->end_time->format('M d, Y g:i A') }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $now = now();
                                                @endphp
                                                @if($ann->start_time > $now)
                                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                                @elseif($ann->end_time < $now)
                                                    <span class="badge bg-light text-muted">Ended</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $ann->creator->name ?? 'System' }}
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this announcement?')" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm border-0" style="padding: 4px 8px;">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($announcements->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $announcements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
