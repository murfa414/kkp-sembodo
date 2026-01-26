{{-- 
    Stat Card Component
    Usage: <x-stat-card color="primary" icon="receipt" label="Total Transaksi" :value="$total" />
--}}

@props([
    'color' => 'primary',
    'icon' => 'chart-bar',
    'label' => 'Label',
    'value' => 0
])

<div class="card border-0 shadow-sm bg-{{ $color }} text-white h-100">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="small mb-1 opacity-75">{{ $label }}</p>
                <h3 class="fw-bold mb-0">{{ $value }}</h3>
            </div>
            <i class="fas fa-{{ $icon }} fa-2x opacity-50"></i>
        </div>
    </div>
</div>
