@extends('layouts.app')

@section('title', 'Announcements Bulletin - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold mb-1" style="color: var(--dark);">Campus Announcements Board</h1>
            <p class="text-muted mb-0">Stay informed about campus maintenance outages, notices, and updates.</p>
        </div>
    </div>

    @if($announcements->isEmpty())
        <div class="card shadow-sm border-0 text-center p-5 bg-white" style="border-radius: var(--radius);">
            <div class="card-body py-5">
                <i class="fas fa-bullhorn mb-3" style="font-size: 3.5rem; color: #cbd5e1; opacity: 0.8;"></i>
                <h4 class="font-weight-bold mb-2" style="color: var(--dark);">No Active Announcements</h4>
                <p class="text-muted mb-0">There are currently no active maintenance alerts or notices posted.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($announcements as $ann)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0 hover-lift" style="border-radius: var(--radius); overflow: hidden; transition: var(--transition); border-left: 4px solid {{ $ann->category->color ?? 'var(--warning)' }} !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                        @if($ann->category)
                                            <span class="badge" style="background-color: {{ $ann->category->color }}; font-size: 0.72rem; padding: 4px 10px;">
                                                {{ $ann->category->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 0.72rem; padding: 4px 10px;">
                                                General Notice
                                            </span>
                                        @endif
                                        <span class="text-muted" style="font-size: 0.78rem;">
                                            <i class="far fa-calendar-alt mr-1"></i> Posted {{ $ann->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <h3 class="h5 font-weight-bold mb-3" style="color: var(--dark); line-height: 1.3;">
                                        {{ $ann->title }}
                                    </h3>
                                </div>
                                <div class="text-end text-muted" style="font-size: 0.78rem;">
                                    <div><i class="fas fa-user-circle mr-1"></i> By {{ $ann->creator->name ?? 'System' }}</div>
                                    <div class="mt-1 font-weight-bold" style="color: var(--primary);">
                                        Ends: {{ $ann->end_time->format('M d, g:i A') }}
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-muted" style="font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">
                                {{ Str::limit($ann->content, 180, '...') }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark font-weight-bold" style="font-size: 0.75rem;">
                                    @php
                                        $diff = $ann->end_time->diff(now());
                                    @endphp
                                    @if($ann->end_time > now())
                                        Active for next {{ $diff->d > 0 ? $diff->d . 'd ' : '' }}{{ $diff->h }}h
                                    @else
                                        Ended
                                    @endif
                                </span>
                                <a href="{{ route('announcements.show', $ann->id) }}" class="btn btn-outline-primary py-1.5 px-3 font-weight-bold" style="font-size: 0.8rem; border-radius: 6px;">
                                    Read Full Notice &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($announcements->hasPages())
            <div class="d-flex justify-content-center mt-3 mb-5">
                {{ $announcements->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
