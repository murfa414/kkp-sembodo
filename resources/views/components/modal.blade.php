{{-- 
    Modal Component
    Usage: 
    <x-modal id="modalConfirm" icon="trash-alt" color="warning" title="Konfirmasi">
        <p>Apakah Anda yakin?</p>
        <x-slot:actions>
            <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-danger" onclick="confirmAction()">Ya</button>
        </x-slot:actions>
    </x-modal>
--}}

@props([
    'id',
    'icon' => 'question-circle',
    'color' => 'secondary',
    'title' => 'Konfirmasi',
    'static' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" 
     @if($static) data-bs-backdrop="static" @endif>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-5">
                {{-- Icon --}}
                <div class="mb-3 text-{{ $color }}">
                    <i class="fas fa-{{ $icon }} fa-4x"></i>
                </div>
                
                {{-- Title --}}
                <h5 class="fw-bold mb-2">{{ $title }}</h5>
                
                {{-- Content --}}
                <div class="text-muted px-4">
                    {{ $slot }}
                </div>
                
                {{-- Actions --}}
                @if(isset($actions))
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
