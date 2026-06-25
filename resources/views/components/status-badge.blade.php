@props(['status'])

@php
    $color = '#95a5a6';
    if ($status === 'Pending') $color = '#f39c12';
    elseif ($status === 'In Progress') $color = '#3498db';
    elseif ($status === 'Resolved') $color = '#2ecc71';
    elseif ($status === 'Rejected') $color = '#e74c3c';
@endphp

<span class="badge" style="background-color: {{ $color }}">{{ $status }}</span>
