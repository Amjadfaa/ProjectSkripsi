<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-file-shield"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Upload Dokumen Persyaratan PAS Bandara
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">Kelola berkas resmi persyaratan penerbitan kartu PAS baru dan perpanjangan masa berlaku</p>
            </div>
        </div>
    </x-slot>

    @php
        $totalUploaded = ($dokumenBaru->hasFile() ? 1 : 0) + ($dokumenPerpanjangan->hasFile() ? 1 : 0);
    @endphp

    {{-- HERO BANNER CARD (PERSIS SEPERTI HERO BANNER DI DASHBOARD) --}}
    <div class="relative overflow-hidden rounded-2xl mb-6 text-white p-5 sm:p-7 shadow-lg border border-slate-800/40"
         style="background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e3a8a 100%);">
        
        {{-- Decorative Ambient Light & Aviation Watermarks (Sama Seperti Dashboard) --}}
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-file-shield text-[200px]"></i>
        </div>
        <div class="absolute right-1/4 -top-12 opacity-10 pointer-events-none">
            <i class="fas fa-plane-departure text-[160px]"></i>
        </div>
        <div class="absolute top-0 right-1/3 w-72 h-36 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-400/20 text-blue-200 border border-blue-300/30 text-[10.5px] font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Manajemen Dokumen Resmi PAS Bandara</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Upload Dokumen Persyaratan PAS Bandara
                </h2>
                <p class="text-blue-100/75 text-xs sm:text-sm leading-relaxed font-normal">
                    Unggah dan kelola berkas formulir persyaratan resmi untuk permohonan PAS Baru dan Perpanjangan. Berkas yang aktif akan ditampilkan pada halaman pemohon dan dapat diunduh langsung.
                </p>

                {{-- Badges info --}}
                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-file-lines text-blue-300"></i> PDF, DOC, DOCX, JPG, PNG
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-hard-drive text-amber-300"></i> Maks. 20 MB per file
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-globe text-emerald-300"></i> Akses Publik di Halaman Login
                    </span>
                </div>
            </div>

            {{-- Right CTA / Status Summary --}}
            <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end justify-between gap-3 shrink-0">
                {{-- Quick Status Pill --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-xs">
                    <span class="text-blue-100/80">Status Kelengkapan:</span>
                    <span id="kpiUploadedCount" class="font-bold {{ $totalUploaded === 2 ? 'text-emerald-300' : ($totalUploaded === 1 ? 'text-amber-300' : 'text-white') }}">
                        <i class="fas {{ $totalUploaded === 2 ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                        {{ $totalUploaded }} / 2 Berkas Aktif
                    </span>
                </div>

                {{-- Action Button: Batch Upload Modal --}}
                <button type="button" onclick="openModal('modalBatchUpload')"
                        class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-xs px-4 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <i class="fas fa-bolt-lightning text-slate-900"></i>
                    <span>Unggah 2 Dokumen Sekaligus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- DUA KARTU DOKUMEN PERSYARATAN (PAS BARU & PERPANJANGAN) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- ================================================================ --}}
        {{-- KARTU 1: PERSYARATAN PEMBUATAN PAS (BARU)                       --}}
        {{-- ================================================================ --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition hover:shadow-sm" id="card-dokumen-baru">
            <div class="h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

            {{-- Card Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                        1
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                            PAS Baru
                        </span>
                        <h2 class="font-bold text-sm text-slate-800 mt-0.5 truncate tracking-tight">
                            Persyaratan Pembuatan PAS (Baru)
                        </h2>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div id="status-badge-baru">
                    @if($dokumenBaru->hasFile())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Berkas Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Belum Ada Berkas
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between gap-5">
                {{-- Container File Aktif --}}
                <div id="active-file-container-baru">
                    @if($dokumenBaru->hasFile())
                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 flex flex-col gap-3">
                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 shadow-2xs {{ $dokumenBaru->isPdf() ? 'bg-rose-50 text-rose-600 border border-rose-200/80' : ($dokumenBaru->isImage() ? 'bg-purple-50 text-purple-600 border border-purple-200/80' : 'bg-blue-50 text-blue-600 border border-blue-200/80') }}">
                                    @if($dokumenBaru->isPdf())
                                        <i class="fas fa-file-pdf"></i>
                                    @elseif($dokumenBaru->isImage())
                                        <i class="fas fa-file-image"></i>
                                    @else
                                        <i class="fas fa-file-word"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-slate-200 text-slate-700">
                                            {{ strtoupper($dokumenBaru->file_type ?? 'FILE') }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-mono font-medium">
                                            {{ $dokumenBaru->formatted_size }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 truncate mt-1" title="{{ $dokumenBaru->file_name }}">
                                        {{ $dokumenBaru->file_name }}
                                    </p>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400 mt-1">
                                        <span><i class="far fa-clock mr-1"></i>{{ $dokumenBaru->updated_at ? $dokumenBaru->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                        @if($dokumenBaru->uploader)
                                            <span><i class="far fa-user mr-1"></i>{{ $dokumenBaru->uploader->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($dokumenBaru->deskripsi)
                                <div class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200/80 leading-relaxed">
                                    <span class="font-bold text-slate-700 block mb-0.5"><i class="fas fa-circle-info text-blue-500 mr-1"></i>Petunjuk:</span>
                                    {{ $dokumenBaru->deskripsi }}
                                </div>
                            @endif

                            {{-- File Actions Toolbar --}}
                            <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            onclick="openPreviewModal('{{ route('administrator.dokumen-persyaratan.preview', $dokumenBaru->id) }}', '{{ addslashes($dokumenBaru->file_name) }}', '{{ $dokumenBaru->file_type }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition cursor-pointer">
                                        <i class="fas fa-eye"></i> Pratinjau
                                    </button>
                                    <a href="{{ route('administrator.dokumen-persyaratan.download', $dokumenBaru->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs transition">
                                        <i class="fas fa-download text-emerald-600"></i> Unduh
                                    </a>
                                </div>

                                <button type="button" 
                                        onclick="confirmDeleteDoc({{ $dokumenBaru->id }}, '{{ addslashes($dokumenBaru->nama_dokumen) }}', 'baru')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 transition cursor-pointer">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Toggle Ganti File Button --}}
                <div id="btn-toggle-box-baru" class="{{ $dokumenBaru->hasFile() ? 'block' : 'hidden' }}">
                    <button type="button" 
                            onclick="toggleUploadSection('baru')" 
                            id="btn-toggle-baru"
                            class="w-full py-2.5 px-4 text-xs font-bold text-blue-700 bg-blue-50/70 hover:bg-blue-100/70 rounded-xl border border-blue-200 flex items-center justify-center gap-2 transition cursor-pointer">
                        <i class="fas fa-rotate" id="icon-toggle-baru"></i>
                        <span id="text-toggle-baru">Ganti / Perbarui Berkas Dokumen</span>
                    </button>
                </div>

                {{-- Upload Section Form --}}
                <div id="upload-section-baru" class="{{ $dokumenBaru->hasFile() ? 'hidden' : 'block' }}">
                    <form id="form-upload-baru" onsubmit="handleFormSubmitAjax('baru', event)" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5" id="label-file-baru">
                                {{ $dokumenBaru->hasFile() ? 'Unggah Berkas Pengganti:' : 'Pilih Berkas Persyaratan:' }}
                            </label>

                            {{-- Dropzone --}}
                            <div id="dropzone-baru" 
                                 onclick="document.getElementById('file-baru').click()"
                                 class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-blue-50/20 transition cursor-pointer relative">
                                <input type="file" 
                                       name="file" 
                                       id="file-baru" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" 
                                       onchange="handleFileSelect('baru', this)">

                                <div id="dropzone-default-baru">
                                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 inline-flex items-center justify-center text-xl mb-2 shadow-2xs">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">
                                        Klik untuk memilih berkas <span class="text-slate-400 font-normal">atau tarik ke sini</span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        Format: PDF, Word (DOC/DOCX), atau Gambar (Maks. 20 MB)
                                    </p>
                                </div>

                                {{-- Selected File Preview --}}
                                <div id="file-info-baru" class="hidden flex items-center justify-between gap-3 bg-blue-50 border border-blue-200 rounded-xl p-3 text-left">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <i class="fas fa-file-circle-check text-blue-600 text-lg shrink-0"></i>
                                        <div class="truncate">
                                            <p id="file-name-baru" class="text-xs font-bold text-blue-950 truncate"></p>
                                            <p id="file-size-baru" class="text-[11px] text-blue-600 font-mono mt-0.5"></p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="cancelFileSelection('baru', event)" 
                                            class="w-6 h-6 rounded-lg bg-blue-200/60 hover:bg-rose-100 hover:text-rose-600 text-blue-800 flex items-center justify-center text-xs transition shrink-0 cursor-pointer" 
                                            title="Batalkan pilihan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Input Deskripsi / Petunjuk --}}
                        <div>
                            <label for="deskripsi-baru" class="block text-xs font-semibold text-slate-700 mb-1">
                                Catatan / Petunjuk Persyaratan (Opsional):
                            </label>
                            <textarea name="deskripsi" 
                                      id="deskripsi-baru" 
                                      rows="2" 
                                      placeholder="Tuliskan catatan kelengkapan berkas untuk pemohon..."
                                      class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">{{ old('deskripsi', $dokumenBaru->deskripsi) }}</textarea>
                        </div>

                        {{-- Tombol Submit --}}
                        <div>
                            <button type="submit" 
                                    id="btn-submit-baru"
                                    class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 transition cursor-pointer">
                                <i class="fas fa-upload text-blue-200"></i>
                                <span id="btn-text-baru">{{ $dokumenBaru->hasFile() ? 'Simpan Berkas Baru' : 'Unggah Dokumen PAS Baru' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- KARTU 2: PERSYARATAN PERPANJANGAN PAS BANDARA                  --}}
        {{-- ================================================================ --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition hover:shadow-sm" id="card-dokumen-perpanjangan">
            <div class="h-1.5 bg-gradient-to-r from-amber-500 to-orange-500"></div>

            {{-- Card Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                        2
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">
                            Perpanjangan PAS
                        </span>
                        <h2 class="font-bold text-sm text-slate-800 mt-0.5 truncate tracking-tight">
                            Persyaratan Perpanjangan PAS Bandara
                        </h2>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div id="status-badge-perpanjangan">
                    @if($dokumenPerpanjangan->hasFile())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Berkas Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Belum Ada Berkas
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between gap-5">
                {{-- Container File Aktif --}}
                <div id="active-file-container-perpanjangan">
                    @if($dokumenPerpanjangan->hasFile())
                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 flex flex-col gap-3">
                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 shadow-2xs {{ $dokumenPerpanjangan->isPdf() ? 'bg-rose-50 text-rose-600 border border-rose-200/80' : ($dokumenPerpanjangan->isImage() ? 'bg-purple-50 text-purple-600 border border-purple-200/80' : 'bg-amber-50 text-amber-600 border border-amber-200/80') }}">
                                    @if($dokumenPerpanjangan->isPdf())
                                        <i class="fas fa-file-pdf"></i>
                                    @elseif($dokumenPerpanjangan->isImage())
                                        <i class="fas fa-file-image"></i>
                                    @else
                                        <i class="fas fa-file-word"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-slate-200 text-slate-700">
                                            {{ strtoupper($dokumenPerpanjangan->file_type ?? 'FILE') }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-mono font-medium">
                                            {{ $dokumenPerpanjangan->formatted_size }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 truncate mt-1" title="{{ $dokumenPerpanjangan->file_name }}">
                                        {{ $dokumenPerpanjangan->file_name }}
                                    </p>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400 mt-1">
                                        <span><i class="far fa-clock mr-1"></i>{{ $dokumenPerpanjangan->updated_at ? $dokumenPerpanjangan->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                        @if($dokumenPerpanjangan->uploader)
                                            <span><i class="far fa-user mr-1"></i>{{ $dokumenPerpanjangan->uploader->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($dokumenPerpanjangan->deskripsi)
                                <div class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200/80 leading-relaxed">
                                    <span class="font-bold text-slate-700 block mb-0.5"><i class="fas fa-circle-info text-amber-500 mr-1"></i>Petunjuk:</span>
                                    {{ $dokumenPerpanjangan->deskripsi }}
                                </div>
                            @endif

                            {{-- File Actions Toolbar --}}
                            <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            onclick="openPreviewModal('{{ route('administrator.dokumen-persyaratan.preview', $dokumenPerpanjangan->id) }}', '{{ addslashes($dokumenPerpanjangan->file_name) }}', '{{ $dokumenPerpanjangan->file_type }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition cursor-pointer">
                                        <i class="fas fa-eye"></i> Pratinjau
                                    </button>
                                    <a href="{{ route('administrator.dokumen-persyaratan.download', $dokumenPerpanjangan->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs transition">
                                        <i class="fas fa-download text-emerald-600"></i> Unduh
                                    </a>
                                </div>

                                <button type="button" 
                                        onclick="confirmDeleteDoc({{ $dokumenPerpanjangan->id }}, '{{ addslashes($dokumenPerpanjangan->nama_dokumen) }}', 'perpanjangan')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 transition cursor-pointer">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Toggle Ganti File Button --}}
                <div id="btn-toggle-box-perpanjangan" class="{{ $dokumenPerpanjangan->hasFile() ? 'block' : 'hidden' }}">
                    <button type="button" 
                            onclick="toggleUploadSection('perpanjangan')" 
                            id="btn-toggle-perpanjangan"
                            class="w-full py-2.5 px-4 text-xs font-bold text-amber-700 bg-amber-50/70 hover:bg-amber-100/70 rounded-xl border border-amber-200 flex items-center justify-center gap-2 transition cursor-pointer">
                        <i class="fas fa-rotate" id="icon-toggle-perpanjangan"></i>
                        <span id="text-toggle-perpanjangan">Ganti / Perbarui Berkas Dokumen</span>
                    </button>
                </div>

                {{-- Upload Section Form --}}
                <div id="upload-section-perpanjangan" class="{{ $dokumenPerpanjangan->hasFile() ? 'hidden' : 'block' }}">
                    <form id="form-upload-perpanjangan" onsubmit="handleFormSubmitAjax('perpanjangan', event)" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5" id="label-file-perpanjangan">
                                {{ $dokumenPerpanjangan->hasFile() ? 'Unggah Berkas Pengganti:' : 'Pilih Berkas Persyaratan:' }}
                            </label>

                            {{-- Dropzone --}}
                            <div id="dropzone-perpanjangan" 
                                 onclick="document.getElementById('file-perpanjangan').click()"
                                 class="border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-amber-50/20 transition cursor-pointer relative">
                                <input type="file" 
                                       name="file" 
                                       id="file-perpanjangan" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" 
                                       onchange="handleFileSelect('perpanjangan', this)">

                                <div id="dropzone-default-perpanjangan">
                                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 inline-flex items-center justify-center text-xl mb-2 shadow-2xs">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">
                                        Klik untuk memilih berkas <span class="text-slate-400 font-normal">atau tarik ke sini</span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        Format: PDF, Word (DOC/DOCX), atau Gambar (Maks. 20 MB)
                                    </p>
                                </div>

                                {{-- Selected File Preview --}}
                                <div id="file-info-perpanjangan" class="hidden flex items-center justify-between gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-left">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <i class="fas fa-file-circle-check text-amber-600 text-lg shrink-0"></i>
                                        <div class="truncate">
                                            <p id="file-name-perpanjangan" class="text-xs font-bold text-amber-950 truncate"></p>
                                            <p id="file-size-perpanjangan" class="text-[11px] text-amber-700 font-mono mt-0.5"></p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="cancelFileSelection('perpanjangan', event)" 
                                            class="w-6 h-6 rounded-lg bg-amber-200/60 hover:bg-rose-100 hover:text-rose-600 text-amber-900 flex items-center justify-center text-xs transition shrink-0 cursor-pointer" 
                                            title="Batalkan pilihan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Input Deskripsi / Petunjuk --}}
                        <div>
                            <label for="deskripsi-perpanjangan" class="block text-xs font-semibold text-slate-700 mb-1">
                                Catatan / Petunjuk Persyaratan (Opsional):
                            </label>
                            <textarea name="deskripsi" 
                                      id="deskripsi-perpanjangan" 
                                      rows="2" 
                                      placeholder="Tuliskan catatan kelengkapan berkas untuk perpanjangan..."
                                      class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition shadow-2xs">{{ old('deskripsi', $dokumenPerpanjangan->deskripsi) }}</textarea>
                        </div>

                        {{-- Tombol Submit --}}
                        <div>
                            <button type="submit" 
                                    id="btn-submit-perpanjangan"
                                    class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-md shadow-amber-500/20 flex items-center justify-center gap-2 transition cursor-pointer">
                                <i class="fas fa-upload text-amber-200"></i>
                                <span id="btn-text-perpanjangan">{{ $dokumenPerpanjangan->hasFile() ? 'Simpan Berkas Baru' : 'Unggah Dokumen Perpanjangan' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- ======================================================================== -->
    {{-- REUSABLE MODALS (USING x-modal & x-modal-delete)                        --}}
    <!-- ======================================================================== -->

    {{-- 1. MODAL BATCH UPLOAD (UNGGAH 2 DOKUMEN SEKALIGUS) --}}
    <x-modal id="modalBatchUpload"
             title="Unggah Dua Dokumen Sekaligus"
             subtitle="Perbarui berkas persyaratan PAS Baru dan Perpanjangan dalam satu formulir"
             icon="fas fa-bolt-lightning"
             iconColor="bg-amber-50 text-amber-600 border-amber-100"
             maxWidth="max-w-2xl">
        <form id="form-upload-batch" onsubmit="handleBatchUploadSubmit(event)">
            @csrf
            <div class="space-y-4">
                {{-- Dropzone 1: Dokumen PAS Baru --}}
                <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/30">
                    <label class="block text-xs font-bold text-slate-800 mb-1.5 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-blue-600 text-white text-[10px] font-black inline-flex items-center justify-center">1</span>
                        <span>Berkas Dokumen PAS Baru</span>
                    </label>
                    <input type="file" name="file_baru" id="batch-file-baru" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <input type="text" name="deskripsi_baru" placeholder="Petunjuk/catatan untuk PAS Baru (opsional)..."
                           class="w-full mt-2 px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 shadow-2xs">
                </div>

                {{-- Dropzone 2: Dokumen Perpanjangan PAS --}}
                <div class="p-4 rounded-xl border border-amber-100 bg-amber-50/30">
                    <label class="block text-xs font-bold text-slate-800 mb-1.5 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-amber-600 text-white text-[10px] font-black inline-flex items-center justify-center">2</span>
                        <span>Berkas Dokumen Perpanjangan PAS</span>
                    </label>
                    <input type="file" name="file_perpanjangan" id="batch-file-perpanjangan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    <input type="text" name="deskripsi_perpanjangan" placeholder="Petunjuk/catatan untuk Perpanjangan (opsional)..."
                           class="w-full mt-2 px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 shadow-2xs">
                </div>

                <div class="text-[11px] text-slate-400">
                    <i class="fas fa-info-circle mr-1 text-slate-400"></i> Anda dapat memilih salah satu berkas atau mengisi kedua berkas sekaligus. Ukuran maksimal 20 MB per file.
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" onclick="closeModal('modalBatchUpload')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition cursor-pointer">
                Batal
            </button>
            <button type="submit" id="btnSubmitBatch" form="form-upload-batch"
                    class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-md shadow-amber-500/20 transition cursor-pointer">
                <i class="fas fa-upload mr-1"></i> Unggah Sekarang
            </button>
        </x-slot>
    </x-modal>

    {{-- 2. MODAL PRATINJAU DOKUMEN (INLINE PREVIEW) --}}
    <x-modal id="modalPreviewDoc"
             title="Pratinjau Dokumen Persyaratan"
             subtitle="Lihat langsung berkas dokumen persyaratan resmi"
             icon="fas fa-eye"
             iconColor="bg-blue-50 text-blue-600 border-blue-100"
             maxWidth="max-w-4xl">
        <div id="previewDocContent" class="w-full flex items-center justify-center min-h-[450px] bg-slate-50 rounded-xl overflow-hidden border border-slate-200/80">
            <div class="text-center text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-xs">Memuat pratinjau...</p>
            </div>
        </div>
        <x-slot name="footer">
            <a id="btnPreviewDownload" href="#" target="_blank"
               class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition inline-flex items-center gap-1.5">
                <i class="fas fa-download"></i> Unduh Berkas
            </a>
            <button type="button" onclick="closeModal('modalPreviewDoc')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition cursor-pointer">
                Tutup
            </button>
        </x-slot>
    </x-modal>

    {{-- 3. REUSABLE MODAL DELETE --}}
    <x-modal-delete id="globalDeleteModal" />

    <!-- ======================================================================== -->
    {{-- JAVASCRIPT & AJAX CONTROLLER                                            --}}
    <!-- ======================================================================== -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Modal Helpers (Vanilla JS instant toggle)
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // Toggle form ganti berkas
        function toggleUploadSection(kategori) {
            const section = document.getElementById('upload-section-' + kategori);
            const textSpan = document.getElementById('text-toggle-' + kategori);
            const icon = document.getElementById('icon-toggle-' + kategori);

            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                textSpan.textContent = 'Tutup Form Penggantian Berkas';
                icon.className = 'fas fa-chevron-up';
            } else {
                section.classList.add('hidden');
                textSpan.textContent = 'Ganti / Perbarui Berkas Dokumen';
                icon.className = 'fas fa-rotate';
            }
        }

        function handleFileSelect(kategori, input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const maxBytes = 20 * 1024 * 1024; // 20 MB

            if (file.size > maxBytes) {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Ukuran Berkas Terlalu Besar', 'Maksimal ukuran berkas adalah 20 MB.');
                } else {
                    alert('Ukuran berkas melebihi batas maksimal 20 MB.');
                }
                input.value = '';
                return;
            }

            const allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Format Tidak Didukung', 'Pilih berkas dengan format PDF, Word (DOC/DOCX), atau Gambar (JPG/PNG).');
                } else {
                    alert('Format file tidak didukung.');
                }
                input.value = '';
                return;
            }

            document.getElementById('dropzone-default-' + kategori).classList.add('hidden');
            document.getElementById('file-info-' + kategori).classList.remove('hidden');
            document.getElementById('file-name-' + kategori).textContent = file.name;
            document.getElementById('file-size-' + kategori).textContent = 'Ukuran: ' + formatBytes(file.size);
        }

        function cancelFileSelection(kategori, event) {
            if (event) event.stopPropagation();
            const input = document.getElementById('file-' + kategori);
            if (input) input.value = '';
            const def = document.getElementById('dropzone-default-' + kategori);
            const info = document.getElementById('file-info-' + kategori);
            if (def) def.classList.remove('hidden');
            if (info) info.classList.add('hidden');
        }

        // Drag & Drop listener
        ['baru', 'perpanjangan'].forEach(kategori => {
            const dropzone = document.getElementById('dropzone-' + kategori);
            const input = document.getElementById('file-' + kategori);
            if (!dropzone || !input) return;

            ['dragenter', 'dragover'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-blue-500', 'bg-blue-50/30');
                });
            });

            ['dragleave', 'drop'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-blue-500', 'bg-blue-50/30');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    handleFileSelect(kategori, input);
                }
            });
        });

        // Submit Upload AJAX
        async function handleFormSubmitAjax(kategori, event) {
            event.preventDefault();
            const input = document.getElementById('file-' + kategori);
            if (!input.files || !input.files[0]) {
                if (typeof window.showToast === 'function') {
                    window.showToast('warning', 'Pilih Berkas', 'Silakan pilih berkas dokumen yang ingin diunggah.');
                }
                return;
            }

            const btn = document.getElementById('btn-submit-' + kategori);
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunggah berkas...';

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', input.files[0]);
            const desc = document.getElementById('deskripsi-' + kategori);
            if (desc && desc.value) {
                formData.append('deskripsi', desc.value);
            }

            try {
                const res = await fetch("{{ url('administrator/dokumen-persyaratan/upload') }}/" + kategori, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Upload Berhasil!', data.message);
                    }
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    const msg = (data.errors && data.errors.file) ? data.errors.file.join(' ') : (data.message || 'Gagal mengunggah berkas.');
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Gagal Mengunggah', msg);
                    } else {
                        alert(msg);
                    }
                }
            } catch(e) {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Kesalahan Jaringan', 'Terjadi kesalahan koneksi saat mengunggah berkas.');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // Submit Batch Upload AJAX
        async function handleBatchUploadSubmit(e) {
            e.preventDefault();
            const fileBaru = document.getElementById('batch-file-baru');
            const filePerp = document.getElementById('batch-file-perpanjangan');

            if ((!fileBaru.files || !fileBaru.files[0]) && (!filePerp.files || !filePerp.files[0])) {
                if (typeof window.showToast === 'function') {
                    window.showToast('warning', 'Pilih Berkas', 'Pilih setidaknya satu berkas untuk diunggah.');
                }
                return;
            }

            const btn = document.getElementById('btnSubmitBatch');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunggah...';

            const form = document.getElementById('form-upload-batch');
            const formData = new FormData(form);

            try {
                const res = await fetch("{{ route('administrator.dokumen-persyaratan.upload-batch') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    closeModal('modalBatchUpload');
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Berhasil!', data.message);
                    }
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Gagal Mengunggah', data.message || 'Terjadi kesalahan saat unggah batch.');
                    }
                }
            } catch(err) {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Kesalahan Koneksi', 'Gagal menghubungi server.');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // Open Document Preview Modal
        function openPreviewModal(url, filename, type) {
            const container = document.getElementById('previewDocContent');
            const btnDownload = document.getElementById('btnPreviewDownload');
            if (btnDownload) btnDownload.href = url.replace('/preview', '/download');

            const ext = (type || '').toLowerCase();
            if (ext === 'pdf' || url.endsWith('.pdf')) {
                container.innerHTML = `
                    <iframe src="${url}" class="w-full h-[520px] rounded-xl border-0" title="${filename}"></iframe>
                `;
            } else if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                container.innerHTML = `
                    <div class="p-4 max-h-[520px] overflow-auto flex items-center justify-center">
                        <img src="${url}" alt="${filename}" class="max-h-[480px] max-w-full rounded-xl object-contain shadow-md">
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <div class="text-center p-8">
                        <i class="fas fa-file-word text-blue-600 text-5xl mb-3"></i>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">${filename}</h4>
                        <p class="text-xs text-slate-400 mb-4">Berkas Word tidak dapat dipratinjau secara langsung di browser.</p>
                        <a href="${url.replace('/preview', '/download')}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-xs">
                            <i class="fas fa-download"></i> Unduh File untuk Membaca
                        </a>
                    </div>
                `;
            }

            openModal('modalPreviewDoc');
        }

        // Confirm Delete using Reusable Modal Delete
        function confirmDeleteDoc(id, nama, kategori) {
            window.openDeleteModal({
                id: 'globalDeleteModal',
                action: `{{ url('/administrator/dokumen-persyaratan') }}/${id}`,
                title: 'Hapus Berkas Persyaratan?',
                message: 'Berkas dokumen persyaratan ini akan dihapus dari server dan tidak dapat diunduh lagi oleh pemohon.',
                targetName: nama,
                btnText: 'Ya, Hapus Berkas'
            });
        }

        // Escape Key Modal Listener
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.app-modal:not(.hidden)');
                openModals.forEach(m => closeModal(m.id));
            }
        });
    </script>
</x-app-layout>
