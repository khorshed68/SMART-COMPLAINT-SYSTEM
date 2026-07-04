@extends('layouts.app')

@section('title', $announcement->title . ' - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in" style="max-width: 800px;">
    <!-- Breadcrumbs navigation -->
    <div class="mb-4">
        <a href="/announcements" class="text-muted" style="text-decoration: none;">
            <i class="fas fa-chevron-left mr-1"></i> Back to Announcements Board
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: var(--radius); overflow: hidden; border-left: 4px solid {{ $announcement->category->color ?? 'var(--warning)' }} !important;">
        <div class="card-body p-4">
            <!-- Header Metadata -->
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4 pb-3 border-bottom" style="border-bottom: 1px solid #edf2f7 !important;">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        @if($announcement->category)
                            <span class="badge" style="background-color: {{ $announcement->category->color }}; font-size: 0.72rem; padding: 4px 10px;">
                                {{ $announcement->category->name }}
                            </span>
                        @else
                            <span class="badge bg-warning text-dark" style="font-size: 0.72rem; padding: 4px 10px;">
                                General Notice
                            </span>
                        @endif
                        <span class="text-muted" style="font-size: 0.78rem;">
                            Posted {{ $announcement->created_at->format('M d, Y g:i A') }} ({{ $announcement->created_at->diffForHumans() }})
                        </span>
                    </div>
                    <h1 class="h4 font-weight-bold mb-0" style="color: var(--dark); line-height: 1.3;">
                        {{ $announcement->title }}
                    </h1>
                </div>
                <div class="text-end text-muted" style="font-size: 0.78rem;">
                    <div><i class="fas fa-user-circle mr-1"></i> Posted by {{ $announcement->creator->name ?? 'System Admin' }}</div>
                    <div class="mt-1 font-weight-bold" style="color: var(--primary);">
                        Active: {{ $announcement->start_time->format('M d, g:i A') }} - {{ $announcement->end_time->format('M d, g:i A') }}
                    </div>
                </div>
            </div>

            <!-- Full Body Notice -->
            <div class="announcement-content text-muted" style="font-size: 0.95rem; line-height: 1.6; white-space: pre-line; margin-bottom: 30px;">
                {!! nl2br(e($announcement->content)) !!}
            </div>

            <!-- Footer warning if category specific -->
            @if($announcement->category)
                <div class="p-3 bg-light border" style="border-radius: 8px; border-left: 4px solid {{ $announcement->category->color }} !important; font-size: 0.85rem;">
                    <i class="fas fa-info-circle mr-1" style="color: {{ $announcement->category->color }};"></i>
                    <strong>Category Notice:</strong> This outage/maintenance affects the <strong>{{ $announcement->category->name }}</strong> category. A warning is currently active on the complaint submission page for this category to prevent duplicate tickets.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
