@props([
    'title' => '',
    'subtitle' => '',
    'icon' => '',
    'iconColor' => 'bg-blue-50 text-blue-600',
])

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
    <!-- Card Top Header (Title & Main Action Buttons) -->
    @if($title || isset($actions))
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center text-base shrink-0 shadow-2xs">
                        <i class="{{ $icon }}"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight leading-tight">
                        {{ $title }}
                    </h2>
                    @if($subtitle)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @if(isset($actions))
                <div class="flex items-center gap-2 flex-wrap">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <!-- Filter / Search Toolbar Slot -->
    @if(isset($filters))
        <div class="px-5 sm:px-6 py-3.5 border-b border-slate-100 bg-slate-50/40">
            {{ $filters }}
        </div>
    @endif

    <!-- Bulk Action / Selection Toolbar Slot -->
    @if(isset($bulkActions))
        <div class="px-5 sm:px-6 py-2.5 bg-blue-50/50 border-b border-blue-100/80 flex items-center justify-between flex-wrap gap-2 text-xs">
            {{ $bulkActions }}
        </div>
    @endif

    <!-- Table Container -->
    <div class="relative overflow-x-auto custom-scrollbar">
        {{ $slot }}
    </div>

    <!-- Footer / Pagination Slot -->
    @if(isset($footer))
        <div>
            {{ $footer }}
        </div>
    @endif
</div>
