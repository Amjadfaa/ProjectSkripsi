<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Upload Template Kartu PAS</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('administrator.template-kartu.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Template
            </a>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm">
                <strong class="font-bold">Gagal Menyimpan Data!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('administrator.template-kartu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- Kolom Kiri: Form Detail & Upload Gambar --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Informasi Utama Template --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-base text-gray-800 flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                            <i class="fas fa-info-circle text-blue-600"></i> Informasi Template
                        </h3>

                        <div class="space-y-4">
                            {{-- Nama Template --}}
                            <div>
                                <label for="nama_template" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Nama Template <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama_template" id="nama_template" value="{{ old('nama_template') }}"
                                       placeholder="Contoh: Template PAS Kuning (Terminal & Penumpang)"
                                       required
                                       class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-[11px] text-gray-400 mt-1">Beri nama yang jelas untuk identifikasi fungsi kartu</p>
                            </div>

                            {{-- Kategori & Tema Warna Kartu --}}
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Kategori & Tema Warna Kartu <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Pilih kategori kartu PAS sesuai zona akses bandara:</p>

                                {{-- Category Cards / Options --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="kategoriSelector">
                                    {{-- Kuning --}}
                                    <button type="button" onclick="selectKategori('kuning', 'Kuning (Terminal / Penumpang)', '#FACC15', '#000000')"
                                            id="btn-cat-kuning"
                                            class="category-btn text-left p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-amber-50/60 border-amber-300 hover:border-amber-400">
                                        <span class="w-8 h-8 rounded-lg bg-amber-400 text-amber-950 flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-xs text-gray-900">PAS Kuning</span>
                                                <i class="fas fa-check-circle text-amber-600 check-icon hidden text-sm"></i>
                                            </div>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-snug">Terminal & Area Penumpang</p>
                                        </div>
                                    </button>

                                    {{-- Biru --}}
                                    <button type="button" onclick="selectKategori('biru', 'Biru (Kargo / Airside)', '#2563EB', '#FFFFFF')"
                                            id="btn-cat-biru"
                                            class="category-btn text-left p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-blue-50/60 border-blue-300 hover:border-blue-400">
                                        <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-xs text-gray-900">PAS Biru</span>
                                                <i class="fas fa-check-circle text-blue-600 check-icon hidden text-sm"></i>
                                            </div>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-snug">Kargo & Sisi Udara (Airside)</p>
                                        </div>
                                    </button>

                                    {{-- Merah --}}
                                    <button type="button" onclick="selectKategori('merah', 'Merah (Apron / Vital)', '#EF4444', '#FFFFFF')"
                                            id="btn-cat-merah"
                                            class="category-btn text-left p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-rose-50/60 border-rose-300 hover:border-rose-400">
                                        <span class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-xs text-gray-900">PAS Merah</span>
                                                <i class="fas fa-check-circle text-rose-600 check-icon hidden text-sm"></i>
                                            </div>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-snug">Apron & Keamanan Terbatas</p>
                                        </div>
                                    </button>
                                </div>

                                {{-- Hidden Form Fields --}}
                                <input type="hidden" name="kode_warna" id="kode_warna" value="{{ old('kode_warna', 'kuning') }}">
                                <input type="hidden" name="warna_label" id="warna_label" value="{{ old('warna_label', 'Kuning (Terminal / Penumpang)') }}">
                                <input type="hidden" name="warna_hex" id="warna_hex" value="{{ old('warna_hex', '#FACC15') }}">
                                <input type="hidden" name="warna_teks" id="warna_teks" value="{{ old('warna_teks', '#000000') }}">
                            </div>

                            <input type="hidden" name="is_active" value="1">
                        </div>
                    </div>

                    {{-- Upload Gambar --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                            <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                                <i class="fas fa-file-image text-amber-500"></i> Desain Kartu PAS
                            </h3>
                            <span class="text-rose-500 text-xs font-bold">* Wajib</span>
                        </div>

                        <div id="dropzone"
                             onclick="document.getElementById('gambar_template').click()"
                             class="border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-xl p-6 text-center cursor-pointer bg-gray-50/50 hover:bg-blue-50/20 transition-all flex flex-col items-center justify-center min-h-[200px]">

                            <input type="file" name="gambar_template" id="gambar_template" accept="image/png,image/jpeg,image/webp"
                                   required class="hidden" onchange="previewUploadImage(event)">

                            <div id="uploadPrompt" class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl mb-3">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 mb-1">Klik atau Drag & Drop Gambar Template</h4>
                                <p class="text-xs text-gray-400">Format: PNG, JPG, JPEG, WEBP (Maks 5 MB)</p>
                                <p class="text-[11px] text-blue-600 font-semibold mt-2 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                    Rekomendasi rasio CR-80 kartu ID (± 54mm x 86mm)
                                </p>
                            </div>

                            <div id="previewContainer" class="hidden flex-col items-center space-y-3">
                                <div class="w-48 h-72 rounded-xl overflow-hidden shadow-lg border-2 border-white bg-slate-900">
                                    <img id="imagePreview" src="" alt="Pratinjau Template" class="w-full h-full object-contain">
                                </div>
                                <span class="text-xs font-bold text-blue-600 hover:underline">
                                    <i class="fas fa-sync-alt mr-1"></i> Klik untuk mengganti gambar
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Kolom Kanan: Pemetaan Area Akses --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 flex-wrap gap-2">
                            <div>
                                <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-map-marked-alt text-purple-600"></i> Pemetaan Area Akses
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">Pilih area mana saja yang menggunakan template ini</p>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="toggleSelectAllAreas(true)"
                                        class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[11px] font-bold transition">
                                    Pilih Semua
                                </button>
                                <button type="button" onclick="toggleSelectAllAreas(false)"
                                        class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[11px] font-bold transition">
                                    Batal Semua
                                </button>
                            </div>
                        </div>

                        {{-- Preset Area Cepat --}}
                        <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600 mb-3">
                            <span class="font-bold text-gray-800 block text-[11px] uppercase tracking-wider mb-1.5">Preset Cepat:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="applyAreaPreset(['A','B','C'])"
                                        class="px-2 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg text-[11px] font-bold transition">
                                    Terminal (A, B, C)
                                </button>
                                <button type="button" onclick="applyAreaPreset(['P','L','M','N','O','R','T','V'])"
                                        class="px-2 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-[11px] font-bold transition">
                                    Apron / Vital
                                </button>
                                <button type="button" onclick="applyAreaPreset(['F','G','U'])"
                                        class="px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg text-[11px] font-bold transition">
                                    Kargo & Bagasi
                                </button>
                            </div>
                        </div>

                        {{-- Checklist Area --}}
                        <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
                            @foreach($allAreas as $area)
                                @php
                                    $isClaimed = isset($claimedAreas[strtoupper($area->kode)]);
                                    $claimingTpl = $isClaimed ? $claimedAreas[strtoupper($area->kode)] : null;
                                @endphp
                                @if($isClaimed)
                                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 bg-slate-50 opacity-60 cursor-not-allowed select-none">
                                        <div class="flex items-center min-w-0 pr-2">
                                            <input type="checkbox" disabled class="w-4 h-4 rounded text-slate-400 border-slate-300 mr-3 cursor-not-allowed">
                                            <span class="font-mono font-bold text-xs text-slate-500 bg-slate-200 px-2 py-0.5 rounded mr-2 shrink-0">{{ $area->kode }}</span>
                                            <span class="text-xs font-medium text-slate-500 truncate">{{ $area->keterangan }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border text-slate-600 bg-white border-slate-200 shrink-0" title="Area ini sudah diklaim oleh {{ $claimingTpl['template_name'] }}">
                                            <i class="fas fa-lock text-[9px] mr-1"></i> {{ $claimingTpl['template_name'] }}
                                        </span>
                                    </div>
                                @else
                                    <label class="flex items-center p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                               {{ in_array($area->kode, old('area_akses', [])) ? 'checked' : '' }}
                                               class="area-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 mr-3">
                                        <span class="font-mono font-bold text-sm text-gray-900 bg-gray-100 px-2 py-0.5 rounded mr-2">{{ $area->kode }}</span>
                                        <span class="text-xs font-semibold text-gray-600">{{ $area->keterangan }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        {{-- Footer Actions --}}
                        <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('administrator.template-kartu.index') }}"
                               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                                Batal
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center gap-2">
                                <i class="fas fa-save"></i> Simpan Template
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function selectKategori(code, label, hex, textColor) {
            document.getElementById('kode_warna').value = code;
            document.getElementById('warna_label').value = label;
            document.getElementById('warna_hex').value = hex;
            document.getElementById('warna_teks').value = textColor;

            // Reset all category buttons
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-offset-1', 'ring-blue-600', 'shadow-md', 'scale-[1.02]');
                btn.querySelector('.check-icon')?.classList.add('hidden');
            });

            // Highlight selected
            const activeBtn = document.getElementById(`btn-cat-${code}`);
            if (activeBtn) {
                activeBtn.classList.add('ring-2', 'ring-offset-1', 'ring-blue-600', 'shadow-md', 'scale-[1.02]');
                activeBtn.querySelector('.check-icon')?.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currentCode = document.getElementById('kode_warna').value || 'kuning';
            selectKategori(
                currentCode,
                document.getElementById('warna_label').value || 'Kuning (Terminal / Penumpang)',
                document.getElementById('warna_hex').value || '#FACC15',
                document.getElementById('warna_teks').value || '#000000'
            );
        });

        function previewUploadImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('uploadPrompt').classList.add('hidden');
                    document.getElementById('previewContainer').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function toggleSelectAllAreas(select) {
            document.querySelectorAll('.area-checkbox').forEach(cb => {
                cb.checked = select;
            });
        }

        function applyAreaPreset(codes) {
            toggleSelectAllAreas(false);
            codes.forEach(c => {
                const cb = document.querySelector(`.area-checkbox[value="${c}"]`);
                if (cb) cb.checked = true;
            });
        }
    </script>
</x-app-layout>
