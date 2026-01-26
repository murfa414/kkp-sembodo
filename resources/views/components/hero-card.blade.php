{{-- 
    Hero Card Component
    Usage: 
    <x-hero-card title="Selamat Datang" subtitle="Silakan unggah data">
        <x-slot:action>
            <a href="/upload" class="btn btn-light">Unggah</a>
        </x-slot:action>
    </x-hero-card>
--}}

@props([
    'title' => 'Judul',
    'subtitle' => null,
    'gradient' => 'primary' // primary or reverse
])

@php
    $gradientClass = $gradient === 'reverse' 
        ? 'bg-gradient-primary-reverse' 
        : 'bg-gradient-primary';
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden {{ $gradientClass }}">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0 text-white">
                <h3 class="fw-bold mb-3 text-white">{{ $title }}</h3>
                
                @if($subtitle)
                    <p class="lead mb-4 text-white opacity-85">{{ $subtitle }}</p>
                @endif
                
                @if(isset($action))
                    {{ $action }}
                @endif
            </div>
            
            @if(isset($illustration))
                <div class="col-lg-4 text-center d-none d-lg-block">
                    {{ $illustration }}
                </div>
            @endif
        </div>
    </div>
</div>
