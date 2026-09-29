@props([
    'name' => null,
    'id' => null,
    'show' => false,
    'focusable' => false,
    'title' => '',
    'subtitle' => '',
    'icon' => '',
    'iconColor' => 'bg-blue-50 text-blue-600 border-blue-100',
    'maxWidth' => 'max-w-2xl',
    'zIndex' => 'z-50'
])

@php
    $modalId = $id ?? $name;
@endphp

<div id="{{ $modalId }}"
     class="app-modal fixed inset-0 {{ $zIndex }} hidden items-center justify-center p-3 sm:p-5 bg-slate-900/60"
     onclick="if(event.target === this) closeModal('{{ $modalId }}')">
    
    <div class="bg-white rounded-2xl shadow-2xl {{ $maxWidth }} w-full relative flex flex-col max-h-[92vh] border border-slate-100 overflow-hidden animate-modal-pop"
         onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        @if($title || $icon)
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/70 rounded-t-2xl">
                <div class="flex items-center gap-3 min-w-0 pr-3">
                    @if($icon)
                        <div class="w-10 h-10 rounded-xl {{ $iconColor }} border flex items-center justify-center text-base shrink-0 shadow-2xs">
                            <i class="{{ $icon }}"></i>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight leading-tight truncate">
                            {{ $title }}
                        </h3>
                        @if($subtitle)
                            <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <button type="button" 
                        onclick="closeModal('{{ $modalId }}')" 
                        class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                        title="Tutup (Esc)">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>
        @endif

        <!-- Modal Body -->
        <div class="px-5 sm:px-6 py-5 overflow-y-auto custom-scrollbar flex-1 text-slate-600 text-sm">
            {{ $slot }}
        </div>

        <!-- Modal Footer (Optional) -->
        @if(isset($footer))
            <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 bg-slate-50/60 rounded-b-2xl flex items-center justify-end gap-2.5 shrink-0">
                {{ $footer }}
            </div>
        @endif

    </div>
</div>

<style>
    @keyframes modal-pop {
        0% { opacity: 0; transform: scale(0.97) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-pop {
        animation: modal-pop 0.16s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        will-change: transform, opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }
</style>

<script>
    if (typeof window.openModal === 'undefined') {
        window.openModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        };
        window.closeModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.app-modal:not(.hidden)');
                openModals.forEach(m => window.closeModal(m.id));
            }
        });
    }
</script>
