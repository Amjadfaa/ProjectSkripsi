@props([
    'id' => 'globalDeleteModal'
])

<div id="{{ $id }}" 
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60"
     onclick="if(event.target === this) closeDeleteModal('{{ $id }}')">
    
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full relative flex flex-col border border-slate-100 animate-modal-pop overflow-hidden"
         onclick="event.stopPropagation()">
        
        <!-- Header with Danger Ambient Icon -->
        <div class="pt-7 pb-4 px-6 text-center">
            <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 ring-4 ring-rose-100/60 shadow-inner mb-3">
                <i class="fas fa-trash-can text-2xl relative z-10 text-rose-600"></i>
            </div>
            
            <h3 id="{{ $id }}_title" class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight">
                Konfirmasi Hapus Data
            </h3>
            
            <p id="{{ $id }}_message" class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
            </p>

            <!-- Dynamic Target Name Highlight Badge -->
            <div id="{{ $id }}_targetBox" class="mt-3.5 hidden">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-700 max-w-full">
                    <i class="fas fa-file-lines text-slate-400 text-[11px]"></i>
                    <span id="{{ $id }}_targetName" class="truncate"></span>
                </div>
            </div>

            <!-- Danger Warning Notice Box -->
            <div class="mt-4 p-3 bg-rose-50/60 border border-rose-100 rounded-2xl flex items-start gap-2.5 text-left">
                <div class="w-5 h-5 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-[10px] mt-0.5 font-bold">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="text-[11px] text-rose-900/90 leading-tight">
                    <strong class="font-bold text-rose-800">Peringatan:</strong> Data yang telah dihapus akan dihapus secara permanen dari basis data dan tidak dapat dikembalikan.
                </div>
            </div>
        </div>

        <!-- Form for Submission -->
        <form id="{{ $id }}_form" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" id="{{ $id }}_method" value="DELETE">
            <div id="{{ $id }}_extraInputs"></div>

            <!-- Modal Footer Buttons -->
            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        onclick="closeDeleteModal('{{ $id }}')"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        id="{{ $id }}_submitBtn"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 shadow-md shadow-rose-500/25 flex items-center justify-center gap-1.5 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <i class="fas fa-trash-alt"></i>
                    <span id="{{ $id }}_btnText">Ya, Hapus Data</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    if (typeof window.openDeleteModal === 'undefined') {
        window.openDeleteModal = function(options = {}) {
            const modalId = options.id || 'globalDeleteModal';
            const modal   = document.getElementById(modalId);
            if (!modal) return;

            const form       = document.getElementById(`${modalId}_form`);
            const titleEl    = document.getElementById(`${modalId}_title`);
            const msgEl      = document.getElementById(`${modalId}_message`);
            const targetBox  = document.getElementById(`${modalId}_targetBox`);
            const targetName = document.getElementById(`${modalId}_targetName`);
            const btnText    = document.getElementById(`${modalId}_btnText`);
            const submitBtn  = document.getElementById(`${modalId}_submitBtn`);
            const methodInp  = document.getElementById(`${modalId}_method`);
            const extrasCont = document.getElementById(`${modalId}_extraInputs`);

            if (form && options.action) form.action = options.action;
            if (titleEl && options.title) titleEl.textContent = options.title;
            if (msgEl && options.message) msgEl.textContent = options.message;
            if (btnText && options.btnText) btnText.textContent = options.btnText;
            if (methodInp && options.method) methodInp.value = options.method;

            // Reset submit button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                const defaultIcon = '<i class="fas fa-trash-alt"></i>';
                submitBtn.innerHTML = `${defaultIcon} <span id="${modalId}_btnText">${options.btnText || 'Ya, Hapus Data'}</span>`;
            }

            if (targetBox && targetName) {
                if (options.targetName) {
                    targetName.textContent = options.targetName;
                    targetBox.classList.remove('hidden');
                } else {
                    targetBox.classList.add('hidden');
                }
            }

            if (extrasCont) {
                extrasCont.innerHTML = '';
                if (Array.isArray(options.extraInputs)) {
                    options.extraInputs.forEach(inp => {
                        const el = document.createElement('input');
                        el.type  = 'hidden';
                        el.name  = inp.name;
                        el.value = inp.value;
                        extrasCont.appendChild(el);
                    });
                }
            }

            // Bind loading on submit to prevent double-click
            if (form && !form.dataset.submitHandlerAttached) {
                form.dataset.submitHandlerAttached = 'true';
                form.addEventListener('submit', function() {
                    const btn = document.getElementById(`${modalId}_submitBtn`);
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Menghapus...</span>';
                    }
                });
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        window.closeDeleteModal = function(id = 'globalDeleteModal') {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const delModal = document.getElementById('globalDeleteModal');
                if (delModal && !delModal.classList.contains('hidden')) {
                    window.closeDeleteModal('globalDeleteModal');
                }
            }
        });
    }
</script>
