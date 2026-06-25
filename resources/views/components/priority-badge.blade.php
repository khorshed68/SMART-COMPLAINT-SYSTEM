@props(['priority'])

@php
    $color = '#95a5a6';
    if ($priority === 'High') $color = '#e74c3c';
    elseif ($priority === 'Medium') $color = '#f39c12';
    elseif ($priority === 'Low') $color = '#2ecc71';
@endphp

<span class="badge" style="background-color: {{ $color }}">{{ $priority }}</span>
