@props([
    'type' => null,
    'title' => null,
    'message' => null,
    'dismissible' => true,
    'autoDismiss' => true,
    'autoDismissMs' => 5000,
    'icon' => null,
])

@php
    // Automatically detect session flash messages if no explicit message / type is provided
    $detectedType = $type;
    $detectedMessage = $message;
    $detectedTitle = $title;
    $errorList = [];

    if (empty($detectedMessage) && (!isset($slot) || $slot->isEmpty()) && empty($detectedType)) {
        if (session('success')) {
            $detectedType = 'success';
            $detectedMessage = session('success');
            $detectedTitle = $title ?? 'Berhasil!';
        } elseif (session('status')) {
            $detectedType = 'success';
            $detectedMessage = session('status');
            $detectedTitle = $title ?? 'Berhasil!';
        } elseif (session('error')) {
            $detectedType = 'error';
            $detectedMessage = session('error');
            $detectedTitle = $title ?? 'Terjadi Kesalahan!';
        } elseif (session('warning')) {
            $detectedType = 'warning';
            $detectedMessage = session('warning');
            $detectedTitle = $title ?? 'Perhatian!';
        } elseif (session('info')) {
            $detectedType = 'info';
            $detectedMessage = session('info');
            $detectedTitle = $title ?? 'Informasi!';
        } elseif (isset($errors) && $errors->any()) {
            $detectedType = 'error';
            $detectedTitle = $title ?? 'Periksa Kembali Form Anda!';
            $errorList = $errors->all();
        }
    }

    $finalType = $detectedType ?? 'info';

    $typeConfig = [
        'success' => [
            'bg' => 'bg-gradient-to-r from-emerald-50/95 via-teal-50/70 to-emerald-50/90',
            'border' => 'border-emerald-200/90',
            'title' => 'text-emerald-900',
            'text' => 'text-emerald-700',
            'iconBg' => 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm shadow-emerald-500/25',
            'defaultIcon' => 'fas fa-circle-check',
            'defaultTitle' => 'Berhasil!',
            'progressBar' => 'bg-gradient-to-r from-emerald-500 to-teal-400',
        ],
        'error' => [
            'bg' => 'bg-gradient-to-r from-rose-50/95 via-red-50/70 to-rose-50/90',
            'border' => 'border-rose-200/90',
            'title' => 'text-rose-900',
            'text' => 'text-rose-700',
            'iconBg' => 'bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-sm shadow-rose-500/25',
            'defaultIcon' => 'fas fa-circle-xmark',
            'defaultTitle' => 'Terjadi Kesalahan!',
            'progressBar' => 'bg-gradient-to-r from-rose-500 to-red-500',
        ],
        'danger' => [
            'bg' => 'bg-gradient-to-r from-rose-50/95 via-red-50/70 to-rose-50/90',
            'border' => 'border-rose-200/90',
            'title' => 'text-rose-900',
            'text' => 'text-rose-700',
            'iconBg' => 'bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-sm shadow-rose-500/25',
            'defaultIcon' => 'fas fa-triangle-exclamation',
            'defaultTitle' => 'Peringatan Bahaya!',
            'progressBar' => 'bg-gradient-to-r from-rose-500 to-red-500',
        ],
        'warning' => [
            'bg' => 'bg-gradient-to-r from-amber-50/95 via-yellow-50/70 to-amber-50/90',
            'border' => 'border-amber-200/90',
            'title' => 'text-amber-900',
            'text' => 'text-amber-700',
            'iconBg' => 'bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm shadow-amber-500/25',
            'defaultIcon' => 'fas fa-triangle-exclamation',
            'defaultTitle' => 'Perhatian!',
            'progressBar' => 'bg-gradient-to-r from-amber-500 to-orange-400',
        ],
        'info' => [
            'bg' => 'bg-gradient-to-r from-blue-50/95 via-indigo-50/70 to-blue-50/90',
            'border' => 'border-blue-200/90',
            'title' => 'text-blue-900',
            'text' => 'text-blue-700',
            'iconBg' => 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm shadow-blue-500/25',
            'defaultIcon' => 'fas fa-circle-info',
            'defaultTitle' => 'Informasi',
            'progressBar' => 'bg-gradient-to-r from-blue-500 to-indigo-500',
        ],
    ];

    $cfg = $typeConfig[$finalType] ?? $typeConfig['info'];
    $iconClass = $icon ?? $cfg['defaultIcon'];
    $alertTitle = $detectedTitle ?? $cfg['defaultTitle'];
    $hasContent = !empty($detectedMessage) || !empty($errorList) || (isset($slot) && !$slot->isEmpty());
@endphp

@if($hasContent)
    <div {{ $attributes->merge(['class' => "x-alert-banner rounded-2xl {$cfg['bg']} border {$cfg['border']} p-4 shadow-sm mb-5 transition-all duration-300 flex items-start gap-3.5 relative overflow-hidden animate-alert-slide-down"]) }}
         x-data="{ show: true }"
         x-show="show"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform -translate-y-2 scale-98"
         @if($autoDismiss) data-auto-dismiss="{{ $autoDismissMs }}" @endif>
        
        <!-- Left Icon Badge -->
        <div class="w-9 h-9 rounded-xl {{ $cfg['iconBg'] }} flex items-center justify-center shrink-0 text-sm">
            <i class="{{ $iconClass }}"></i>
        </div>

        <!-- Alert Content -->
        <div class="flex-1 min-w-0 pr-4">
            @if($alertTitle)
                <h4 class="font-bold text-xs sm:text-sm {{ $cfg['title'] }} leading-tight mb-0.5 tracking-tight">
                    {{ $alertTitle }}
                </h4>
            @endif
            
            <div class="text-xs {{ $cfg['text'] }} leading-relaxed">
                @if($detectedMessage)
                    <p class="font-medium">{!! $detectedMessage !!}</p>
                @endif

                @if(!empty($errorList))
                    <ul class="list-disc list-inside mt-1 space-y-0.5 font-medium">
                        @foreach($errorList as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(isset($slot) && !$slot->isEmpty())
                    {{ $slot }}
                @endif
            </div>
        </div>

        <!-- Dismiss Button -->
        @if($dismissible)
            <button type="button" 
                    onclick="dismissAlertComponent(this)"
                    class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-black/5 flex items-center justify-center text-xs transition-colors shrink-0 cursor-pointer"
                    title="Tutup Notifikasi">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        @endif

        @if($autoDismiss)
            <div class="x-alert-progress absolute bottom-0 left-0 h-1 {{ $cfg['progressBar'] }} w-full"
                 style="animation: alert-progress-shrink {{ $autoDismissMs }}ms linear forwards;"></div>
        @endif
    </div>

    <style>
        @keyframes alert-slide-down {
            from { opacity: 0; transform: translateY(-10px) scale(0.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes alert-progress-shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-alert-slide-down {
            animation: alert-slide-down 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <script>
        if (typeof window.dismissAlertComponent === 'undefined') {
            window.dismissAlertComponent = function(el) {
                const banner = el.closest('.x-alert-banner');
                if (!banner) return;
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-10px) scale(0.98)';
                setTimeout(() => {
                    banner.remove();
                }, 250);
            };

            function initAlertAutoDismiss() {
                document.querySelectorAll('.x-alert-banner[data-auto-dismiss]').forEach(banner => {
                    if (banner.dataset.dismissInitialized) return;
                    banner.dataset.dismissInitialized = 'true';
                    const ms = parseInt(banner.getAttribute('data-auto-dismiss')) || 5000;
                    setTimeout(() => {
                        window.dismissAlertComponent(banner);
                    }, ms);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAlertAutoDismiss);
            } else {
                initAlertAutoDismiss();
            }
        }
    </script>
@endif
