{{-- 
    Alert Component
    Usage: <x-alert type="success|danger|warning|info" message="..." />
           <x-alert type="danger" :errors="$errors->all()" />
--}}

@props([
    'type' => 'info',
    'message' => null,
    'errors' => [],
    'dismissible' => true
])

@php
    $icons = [
        'success' => 'check-circle',
        'danger' => 'times-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle'
    ];
    $icon = $icons[$type] ?? 'info-circle';
    $alertId = 'alert-' . uniqid();
@endphp

@if($message || count($errors) > 0)
<div class="alert alert-{{ $type }} {{ $dismissible ? 'alert-dismissible' : '' }} fade show shadow-sm" 
     role="alert" id="{{ $alertId }}">
    <div class="d-flex align-items-start">
        <i class="fas fa-{{ $icon }} me-2 fa-lg mt-1"></i>
        <div class="flex-grow-1">
            @if($message)
                {{ $message }}
            @endif
            
            @if(count($errors) > 0)
                <strong>Terjadi Kesalahan!</strong>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    
    @if($dismissible)
        <button type="button" class="btn-close" 
                onclick="document.getElementById('{{ $alertId }}').remove()"></button>
    @endif
</div>
@endif
