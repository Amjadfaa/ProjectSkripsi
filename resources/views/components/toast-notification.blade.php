{{-- Reusable Toast Notifications & Session Flash Bridge --}}
@php
    $successMessage = session('success') ?? session('status') ?? session('message');
    $errorMessage   = session('error') ?? session('failed');
    if (!$errorMessage && isset($errors) && $errors->any()) {
        $errorMessage = $errors->first();
    }
    $warningMessage = session('warning');
    $infoMessage    = session('info');
@endphp

<div id="toastNotificationContainer" 
     class="fixed top-6 right-6 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm sm:max-w-md w-full px-4 sm:px-0">
    
    {{-- Session Success / Status Toast --}}
    @if($successMessage)
        <div class="toast-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_15px_35px_-8px_rgba(0,0,0,0.14)] border border-emerald-200/90 p-4 flex items-start gap-3.5 transform transition-all duration-300 animate-toast-spring relative overflow-hidden ring-1 ring-black/[0.03]"
             data-auto-dismiss="4000">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shrink-0 text-base shadow-md shadow-emerald-500/25">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5">
                    <h4 class="font-extrabold text-slate-800 text-sm leading-tight tracking-tight">Berhasil!</h4>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{!! $successMessage !!}</p>
            </div>
            <button type="button" onclick="dismissToastItem(this.closest('.toast-item'))" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shrink-0 cursor-pointer transition-all duration-150" title="Tutup Notifikasi">
                <i class="fas fa-xmark text-sm"></i>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 w-full rounded-b-2xl"></div>
        </div>
    @endif

    {{-- Session Error Toast --}}
    @if($errorMessage)
        <div class="toast-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_15px_35px_-8px_rgba(0,0,0,0.14)] border border-rose-200/90 p-4 flex items-start gap-3.5 transform transition-all duration-300 animate-toast-spring relative overflow-hidden ring-1 ring-black/[0.03]"
             data-auto-dismiss="5000">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0 text-base shadow-md shadow-rose-500/25">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5">
                    <h4 class="font-extrabold text-slate-800 text-sm leading-tight tracking-tight">Terjadi Kesalahan!</h4>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{!! $errorMessage !!}</p>
            </div>
            <button type="button" onclick="dismissToastItem(this.closest('.toast-item'))" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shrink-0 cursor-pointer transition-all duration-150" title="Tutup Notifikasi">
                <i class="fas fa-xmark text-sm"></i>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gradient-to-r from-rose-500 via-red-500 to-rose-400 w-full rounded-b-2xl"></div>
        </div>
    @endif

    {{-- Session Warning Toast --}}
    @if($warningMessage)
        <div class="toast-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_15px_35px_-8px_rgba(0,0,0,0.14)] border border-amber-200/90 p-4 flex items-start gap-3.5 transform transition-all duration-300 animate-toast-spring relative overflow-hidden ring-1 ring-black/[0.03]"
             data-auto-dismiss="4500">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center shrink-0 text-base shadow-md shadow-amber-500/25">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5">
                    <h4 class="font-extrabold text-slate-800 text-sm leading-tight tracking-tight">Perhatian!</h4>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{!! $warningMessage !!}</p>
            </div>
            <button type="button" onclick="dismissToastItem(this.closest('.toast-item'))" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shrink-0 cursor-pointer transition-all duration-150" title="Tutup Notifikasi">
                <i class="fas fa-xmark text-sm"></i>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-400 w-full rounded-b-2xl"></div>
        </div>
    @endif

    {{-- Session Info Toast --}}
    @if($infoMessage)
        <div class="toast-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_15px_35px_-8px_rgba(0,0,0,0.14)] border border-blue-200/90 p-4 flex items-start gap-3.5 transform transition-all duration-300 animate-toast-spring relative overflow-hidden ring-1 ring-black/[0.03]"
             data-auto-dismiss="4000">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center shrink-0 text-base shadow-md shadow-blue-500/25">
                <i class="fas fa-circle-info"></i>
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5">
                    <h4 class="font-extrabold text-slate-800 text-sm leading-tight tracking-tight">Informasi</h4>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">{!! $infoMessage !!}</p>
            </div>
            <button type="button" onclick="dismissToastItem(this.closest('.toast-item'))" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shrink-0 cursor-pointer transition-all duration-150" title="Tutup Notifikasi">
                <i class="fas fa-xmark text-sm"></i>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-400 to-blue-400 w-full rounded-b-2xl"></div>
        </div>
    @endif
</div>

<style>
    @keyframes toast-spring {
        0% { opacity: 0; transform: translateX(60px) scale(0.92); }
        70% { transform: translateX(-4px) scale(1.01); }
        100% { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes toast-progress-shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .animate-toast-spring {
        animation: toast-spring 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transform: translateZ(0);
        will-change: transform, opacity;
    }
    .toast-progress {
        animation: toast-progress-shrink linear forwards;
    }
</style>

<script>
    if (typeof window.dismissToastItem === 'undefined') {
        window.dismissToastItem = function(item) {
            if (!item) return;
            item.style.opacity = '0';
            item.style.transform = 'translateX(50px) scale(0.92)';
            setTimeout(() => item.remove(), 250);
        };

        window.initAutoDismissToasts = function() {
            document.querySelectorAll('.toast-item[data-auto-dismiss]').forEach(item => {
                if (item.dataset.dismissInitialized) return;
                item.dataset.dismissInitialized = 'true';

                const ms = parseInt(item.getAttribute('data-auto-dismiss')) || 4000;
                const progress = item.querySelector('.toast-progress');
                if (progress) progress.style.animationDuration = `${ms}ms`;

                setTimeout(() => {
                    window.dismissToastItem(item);
                }, ms);
            });
        };

        // Dynamic JavaScript Toast Notification API
        window.showToast = function(type = 'success', title = 'Berhasil!', message = '') {
            const container = document.getElementById('toastNotificationContainer');
            if (!container) return;

            const config = {
                success: {
                    border: 'border-emerald-200/90',
                    iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/25',
                    icon: 'fas fa-circle-check',
                    pulse: 'bg-emerald-500',
                    progress: 'from-emerald-500 via-teal-400 to-emerald-400',
                    duration: 4000
                },
                error: {
                    border: 'border-rose-200/90',
                    iconBg: 'bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-md shadow-rose-500/25',
                    icon: 'fas fa-circle-xmark',
                    pulse: 'bg-rose-500',
                    progress: 'from-rose-500 via-red-500 to-rose-400',
                    duration: 5000
                },
                warning: {
                    border: 'border-amber-200/90',
                    iconBg: 'bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/25',
                    icon: 'fas fa-triangle-exclamation',
                    pulse: 'bg-amber-500',
                    progress: 'from-amber-500 via-orange-400 to-amber-400',
                    duration: 4500
                },
                info: {
                    border: 'border-blue-200/90',
                    iconBg: 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25',
                    icon: 'fas fa-circle-info',
                    pulse: 'bg-blue-500',
                    progress: 'from-blue-500 via-indigo-400 to-blue-400',
                    duration: 4000
                }
            };

            const c = config[type] || config.info;
            const div = document.createElement('div');
            div.className = `toast-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_15px_35px_-8px_rgba(0,0,0,0.14)] border ${c.border} p-4 flex items-start gap-3.5 transform transition-all duration-300 animate-toast-spring relative overflow-hidden ring-1 ring-black/[0.03]`;
            div.setAttribute('data-auto-dismiss', c.duration);
            div.innerHTML = `
                <div class="w-10 h-10 rounded-xl ${c.iconBg} flex items-center justify-center shrink-0 text-base">
                    <i class="${c.icon}"></i>
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-extrabold text-slate-800 text-sm leading-tight tracking-tight">${title}</h4>
                        <span class="inline-block w-1.5 h-1.5 rounded-full ${c.pulse} animate-pulse"></span>
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">${message}</p>
                </div>
                <button type="button" onclick="dismissToastItem(this.closest('.toast-item'))" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shrink-0 cursor-pointer transition-all duration-150" title="Tutup Notifikasi">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
                <div class="toast-progress absolute bottom-0 left-0 h-1 bg-gradient-to-r ${c.progress} w-full rounded-b-2xl" style="animation-duration: ${c.duration}ms;"></div>
            `;

            container.appendChild(div);

            setTimeout(() => {
                window.dismissToastItem(div);
            }, c.duration);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initAutoDismissToasts);
        } else {
            window.initAutoDismissToasts();
        }
    }
</script>
