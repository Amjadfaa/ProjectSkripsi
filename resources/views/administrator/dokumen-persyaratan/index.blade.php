<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#1e3a5f] flex items-center gap-2">
            <i class="fas fa-file-shield text-[#f0b429]"></i>
            <span>Upload Dokumen Persyaratan PAS Bandara</span>
        </h2>
    </x-slot>

    <style>
        .doc-page-container {
            max-width: 1320px;
            margin: 0 auto;
        }

        /* Banner Informasi */
        .doc-banner {
            background: linear-gradient(135deg, #1e3a5f 0%, #162b46 100%);
            border-radius: 16px;
            padding: 22px 26px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(30, 58, 95, 0.25);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .doc-banner::after {
            content: "\f56d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 20px;
            bottom: -25px;
            font-size: 130px;
            color: rgba(255, 255, 255, 0.04);
            pointer-events: none;
        }
        .banner-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #f0b429;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }
        .format-badge {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #e2e8f0;
            backdrop-filter: blur(4px);
        }

        /* Tombol Upload Batch di Banner */
        .btn-batch-upload {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f0b429;
            color: #1e3a5f;
            font-size: 12.5px;
            font-weight: 800;
            padding: 10px 18px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(240, 180, 41, 0.4);
            transition: all 0.2s ease;
        }
        .btn-batch-upload:hover {
            background: #e0a31b;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(240, 180, 41, 0.5);
        }
        .btn-batch-upload:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Kartu Persyaratan */
        .doc-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .doc-card:hover {
            box-shadow: 0 8px 30px -4px rgba(0, 0, 0, 0.08);
        }
        .card-accent-baru {
            height: 4px;
            background: linear-gradient(90deg, #1e3a5f 0%, #3b82f6 100%);
        }
        .card-accent-perpanjangan {
            height: 4px;
            background: linear-gradient(90deg, #d97706 0%, #f0b429 100%);
        }

        .doc-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .badge-number {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }
        .badge-number-baru {
            background: #eff6ff;
            color: #1e3a5f;
            border: 1px solid #dbeafe;
        }
        .badge-number-perpanjangan {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fef3c7;
        }

        .doc-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .status-uploaded {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .status-empty {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .doc-card-body {
            padding: 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* File Display Terpasang */
        .active-file-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .file-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .icon-pdf { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .icon-doc { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .icon-img { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }

        /* Dropzone Upload */
        .dropzone-container {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px 16px;
            text-align: center;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .dropzone-container:hover, .dropzone-container.dragover {
            border-color: #1e3a5f;
            background: #f8fafc;
            transform: translateY(-1px);
        }
        .dropzone-icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
            transition: transform 0.2s ease;
        }
        .dropzone-container:hover .dropzone-icon-circle {
            transform: scale(1.08);
        }
        .circle-baru {
            background: #eff6ff;
            color: #1e3a5f;
        }
        .circle-perpanjangan {
            background: #fffbeb;
            color: #d97706;
        }

        /* File Selected Box */
        .selected-file-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 10px 14px;
            text-align: left;
        }

        /* Textarea Catatan */
        .doc-textarea {
            width: 100%;
            font-size: 12.5px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            color: #334155;
            transition: border-color 0.2s, box-shadow 0.2s;
            resize: vertical;
            background: #fafbfc;
        }
        .doc-textarea:focus {
            background: #ffffff;
            border-color: #1e3a5f;
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
        }

        /* Tombol Aksi */
        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
        }
        .btn-preview {
            background: #1e3a5f;
            color: #ffffff;
        }
        .btn-preview:hover {
            background: #152840;
            color: #ffffff;
        }
        .btn-download {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-download:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .btn-delete:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Submit Button Utama */
        .btn-submit-main {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .btn-submit-baru {
            background: #1e3a5f;
            color: #ffffff;
        }
        .btn-submit-baru:hover {
            background: #152b47;
            box-shadow: 0 6px 16px rgba(30, 58, 95, 0.25);
        }
        .btn-submit-perpanjangan {
            background: #f0b429;
            color: #1e3a5f;
        }
        .btn-submit-perpanjangan:hover {
            background: #e0a31b;
            box-shadow: 0 6px 16px rgba(240, 180, 41, 0.35);
        }
    </style>

    <div class="doc-page-container">

        {{-- Banner Informasi & Batch Action --}}
        <div class="doc-banner">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="max-w-xl">
                    <div class="banner-tag">
                        <i class="fas fa-shield-halved"></i> Manajemen Dokumen Resmi
                    </div>
                    <h3 class="text-lg font-bold text-white tracking-tight">
                        Dokumen Persyaratan PAS Bandara (Baru & Perpanjangan)
                    </h3>
                    <p class="text-xs text-slate-200 mt-1 leading-relaxed">
                        Unggah berkas acuan/formulir resmi pada masing-masing perihal di bawah. Dokumen ini nantinya akan ditampilkan dan dapat diunduh oleh para pemohon PAS Bandara.
                    </p>
                    <div class="flex items-center gap-2 flex-wrap mt-3">
                        <span class="format-badge"><i class="fas fa-file-pdf mr-1 text-red-400"></i> PDF</span>
                        <span class="format-badge"><i class="fas fa-file-word mr-1 text-blue-400"></i> DOC/DOCX</span>
                        <span class="format-badge"><i class="fas fa-file-image mr-1 text-purple-400"></i> JPG/PNG</span>
                        <span class="format-badge"><i class="fas fa-database mr-1 text-amber-400"></i> Maks. 20 MB</span>
                    </div>
                </div>

                {{-- Tombol Unggah 2 Dokumen Sekaligus --}}
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="button" 
                            id="btn-batch-upload" 
                            onclick="uploadBatchAjax()" 
                            class="btn-batch-upload">
                        <i class="fas fa-bolt text-[#1e3a5f]"></i>
                        <span>Unggah 2 Dokumen Sekaligus</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 2 Kolom Grid Kartu Upload --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            {{-- ========================================================= --}}
            {{-- KARTU 1: PERSYARATAN PEMBUATAN PAS BANDARA (BARU)        --}}
            {{-- ========================================================= --}}
            <div class="doc-card" id="card-dokumen-baru">
                <div class="card-accent-baru"></div>

                {{-- Header Kartu --}}
                <div class="doc-card-header">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="badge-number badge-number-baru">1</div>
                        <div class="min-w-0">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                PAS Baru
                            </span>
                            <h3 class="font-bold text-sm text-slate-800 mt-0.5 truncate">
                                Persyaratan Pembuatan PAS (Baru)
                            </h3>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span id="status-badge-baru" class="doc-status-badge {{ $dokumenBaru->hasFile() ? 'status-uploaded' : 'status-empty' }}">
                        @if($dokumenBaru->hasFile())
                            <i class="fas fa-check-circle text-xs"></i> Sudah Diunggah
                        @else
                            <i class="fas fa-clock text-xs"></i> Belum Ada Berkas
                        @endif
                    </span>
                </div>

                {{-- Body Kartu --}}
                <div class="doc-card-body" id="card-body-baru">

                    {{-- Container File Terpasang --}}
                    <div id="active-file-container-baru">
                        @if($dokumenBaru->hasFile())
                            <div class="active-file-box" id="active-box-baru">
                                <div class="flex items-start gap-3.5">
                                    {{-- Ikon File --}}
                                    <div class="file-icon-box @if($dokumenBaru->isPdf()) icon-pdf @elseif($dokumenBaru->isImage()) icon-img @else icon-doc @endif">
                                        @if($dokumenBaru->isPdf())
                                            <i class="fas fa-file-pdf"></i>
                                        @elseif($dokumenBaru->isImage())
                                            <i class="fas fa-file-image"></i>
                                        @elseif(in_array($dokumenBaru->file_type, ['doc', 'docx']))
                                            <i class="fas fa-file-word"></i>
                                        @else
                                            <i class="fas fa-file-alt"></i>
                                        @endif
                                    </div>

                                    {{-- Detail File --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">
                                                {{ strtoupper($dokumenBaru->file_type ?? 'FILE') }}
                                            </span>
                                            <span class="text-xs text-slate-400 font-medium">
                                                {{ $dokumenBaru->formatted_size }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 truncate mt-0.5" title="{{ $dokumenBaru->file_name }}">
                                            {{ $dokumenBaru->file_name }}
                                        </p>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                                            <span><i class="far fa-clock mr-1 text-slate-400"></i>{{ $dokumenBaru->updated_at ? $dokumenBaru->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                            @if($dokumenBaru->uploader)
                                                <span><i class="far fa-user mr-1 text-slate-400"></i>{{ $dokumenBaru->uploader->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan Jika Ada --}}
                                @if($dokumenBaru->deskripsi)
                                    <div class="text-[11.5px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200/80 leading-relaxed">
                                        <span class="font-bold text-slate-700"><i class="fas fa-info-circle mr-1 text-blue-500"></i>Petunjuk:</span>
                                        {{ $dokumenBaru->deskripsi }}
                                    </div>
                                @endif

                                {{-- Tombol Aksi File --}}
                                <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dokumen-persyaratan.preview', $dokumenBaru->id) }}" 
                                           target="_blank" 
                                           class="btn-action-primary btn-preview">
                                            <i class="fas fa-eye"></i> Pratinjau
                                        </a>
                                        <a href="{{ route('dokumen-persyaratan.download', $dokumenBaru->id) }}" 
                                           class="btn-action-primary btn-download">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                    </div>

                                    <button type="button" 
                                            onclick="confirmDeleteAjax('baru', '{{ $dokumenBaru->nama_dokumen }}', {{ $dokumenBaru->id }})"
                                            class="btn-action-primary btn-delete">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Toggle Ganti File --}}
                    <div id="btn-toggle-box-baru" class="{{ $dokumenBaru->hasFile() ? 'block' : 'hidden' }}">
                        <button type="button" 
                                onclick="toggleUploadSection('baru')" 
                                id="btn-toggle-baru"
                                class="w-full py-2.5 px-4 text-xs font-bold text-blue-700 bg-blue-50/70 hover:bg-blue-100/70 rounded-xl border border-blue-200 flex items-center justify-center gap-2 transition">
                            <i class="fas fa-sync-alt" id="icon-toggle-baru"></i>
                            <span id="text-toggle-baru">Ganti / Perbarui Berkas Dokumen</span>
                        </button>
                    </div>

                    {{-- Form Upload (Selalu tampil jika belum ada file, atau collapsible jika sudah ada file) --}}
                    <div id="upload-section-baru" class="{{ $dokumenBaru->hasFile() ? 'hidden' : 'block' }}">
                        <form id="form-upload-baru" onsubmit="handleFormSubmitAjax('baru', event)" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5" id="label-file-baru">
                                    {{ $dokumenBaru->hasFile() ? 'Unggah Berkas Pengganti:' : 'Pilih Berkas Persyaratan:' }}
                                </label>

                                {{-- Dropzone Bersih & Rapi --}}
                                <div id="dropzone-baru" 
                                     onclick="document.getElementById('file-baru').click()"
                                     class="dropzone-container">
                                    <input type="file" 
                                           name="file" 
                                           id="file-baru" 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           class="hidden" 
                                           onchange="handleFileSelect('baru', this)">

                                    <div id="dropzone-default-baru">
                                        <div class="dropzone-icon-circle circle-baru">
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
                                    <div id="file-info-baru" class="hidden selected-file-preview">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <i class="fas fa-file-circle-check text-blue-600 text-lg flex-shrink-0"></i>
                                            <div class="truncate">
                                                <p id="file-name-baru" class="text-xs font-bold text-blue-950 truncate"></p>
                                                <p id="file-size-baru" class="text-[10.5px] text-blue-600 mt-0.5"></p>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                onclick="cancelFileSelection('baru', event)" 
                                                class="w-6 h-6 rounded-full bg-blue-200/60 hover:bg-red-100 hover:text-red-600 text-blue-800 flex items-center justify-center text-xs transition" 
                                                title="Batalkan pilihan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Input Deskripsi / Catatan --}}
                            <div>
                                <label for="deskripsi-baru" class="block text-xs font-bold text-slate-700 mb-1">
                                    Catatan / Petunjuk Persyaratan (Opsional):
                                </label>
                                <textarea name="deskripsi" 
                                          id="deskripsi-baru" 
                                          rows="2" 
                                          placeholder="Tuliskan catatan berkas, misal: 'Lampirkan surat permohonan asli dan pas foto 3x4 latar merah...'"
                                          class="doc-textarea">{{ old('deskripsi', $dokumenBaru->deskripsi) }}</textarea>
                            </div>

                            {{-- Tombol Submit --}}
                            <div>
                                <button type="submit" 
                                        id="btn-submit-baru"
                                        class="btn-submit-main btn-submit-baru">
                                    <i class="fas fa-upload text-[#f0b429]"></i>
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
            <div class="doc-card" id="card-dokumen-perpanjangan">
                <div class="card-accent-perpanjangan"></div>

                {{-- Header Kartu --}}
                <div class="doc-card-header">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="badge-number badge-number-perpanjangan">2</div>
                        <div class="min-w-0">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2 py-0.5 rounded">
                                Perpanjangan PAS
                            </span>
                            <h3 class="font-bold text-sm text-slate-800 mt-0.5 truncate">
                                Persyaratan Perpanjangan PAS Bandara
                            </h3>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span id="status-badge-perpanjangan" class="doc-status-badge {{ $dokumenPerpanjangan->hasFile() ? 'status-uploaded' : 'status-empty' }}">
                        @if($dokumenPerpanjangan->hasFile())
                            <i class="fas fa-check-circle text-xs"></i> Sudah Diunggah
                        @else
                            <i class="fas fa-clock text-xs"></i> Belum Ada Berkas
                        @endif
                    </span>
                </div>

                {{-- Body Kartu --}}
                <div class="doc-card-body" id="card-body-perpanjangan">

                    {{-- Container File Terpasang --}}
                    <div id="active-file-container-perpanjangan">
                        @if($dokumenPerpanjangan->hasFile())
                            <div class="active-file-box" id="active-box-perpanjangan">
                                <div class="flex items-start gap-3.5">
                                    {{-- Ikon File --}}
                                    <div class="file-icon-box @if($dokumenPerpanjangan->isPdf()) icon-pdf @elseif($dokumenPerpanjangan->isImage()) icon-img @else icon-doc @endif">
                                        @if($dokumenPerpanjangan->isPdf())
                                            <i class="fas fa-file-pdf"></i>
                                        @elseif($dokumenPerpanjangan->isImage())
                                            <i class="fas fa-file-image"></i>
                                        @elseif(in_array($dokumenPerpanjangan->file_type, ['doc', 'docx']))
                                            <i class="fas fa-file-word"></i>
                                        @else
                                            <i class="fas fa-file-alt"></i>
                                        @endif
                                    </div>

                                    {{-- Detail File --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">
                                                {{ strtoupper($dokumenPerpanjangan->file_type ?? 'FILE') }}
                                            </span>
                                            <span class="text-xs text-slate-400 font-medium">
                                                {{ $dokumenPerpanjangan->formatted_size }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 truncate mt-0.5" title="{{ $dokumenPerpanjangan->file_name }}">
                                            {{ $dokumenPerpanjangan->file_name }}
                                        </p>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                                            <span><i class="far fa-clock mr-1 text-slate-400"></i>{{ $dokumenPerpanjangan->updated_at ? $dokumenPerpanjangan->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                            @if($dokumenPerpanjangan->uploader)
                                                <span><i class="far fa-user mr-1 text-slate-400"></i>{{ $dokumenPerpanjangan->uploader->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan Jika Ada --}}
                                @if($dokumenPerpanjangan->deskripsi)
                                    <div class="text-[11.5px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200/80 leading-relaxed">
                                        <span class="font-bold text-slate-700"><i class="fas fa-info-circle mr-1 text-amber-500"></i>Petunjuk:</span>
                                        {{ $dokumenPerpanjangan->deskripsi }}
                                    </div>
                                @endif

                                {{-- Tombol Aksi File --}}
                                <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dokumen-persyaratan.preview', $dokumenPerpanjangan->id) }}" 
                                           target="_blank" 
                                           class="btn-action-primary btn-preview">
                                            <i class="fas fa-eye"></i> Pratinjau
                                        </a>
                                        <a href="{{ route('dokumen-persyaratan.download', $dokumenPerpanjangan->id) }}" 
                                           class="btn-action-primary btn-download">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                    </div>

                                    <button type="button" 
                                            onclick="confirmDeleteAjax('perpanjangan', '{{ $dokumenPerpanjangan->nama_dokumen }}', {{ $dokumenPerpanjangan->id }})"
                                            class="btn-action-primary btn-delete">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Toggle Ganti File --}}
                    <div id="btn-toggle-box-perpanjangan" class="{{ $dokumenPerpanjangan->hasFile() ? 'block' : 'hidden' }}">
                        <button type="button" 
                                onclick="toggleUploadSection('perpanjangan')" 
                                id="btn-toggle-perpanjangan"
                                class="w-full py-2.5 px-4 text-xs font-bold text-amber-800 bg-amber-50/70 hover:bg-amber-100/70 rounded-xl border border-amber-200 flex items-center justify-center gap-2 transition">
                            <i class="fas fa-sync-alt" id="icon-toggle-perpanjangan"></i>
                            <span id="text-toggle-perpanjangan">Ganti / Perbarui Berkas Dokumen</span>
                        </button>
                    </div>

                    {{-- Form Upload (Selalu tampil jika belum ada file, atau collapsible jika sudah ada file) --}}
                    <div id="upload-section-perpanjangan" class="{{ $dokumenPerpanjangan->hasFile() ? 'hidden' : 'block' }}">
                        <form id="form-upload-perpanjangan" onsubmit="handleFormSubmitAjax('perpanjangan', event)" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5" id="label-file-perpanjangan">
                                    {{ $dokumenPerpanjangan->hasFile() ? 'Unggah Berkas Pengganti:' : 'Pilih Berkas Persyaratan:' }}
                                </label>

                                {{-- Dropzone Bersih & Rapi --}}
                                <div id="dropzone-perpanjangan" 
                                     onclick="document.getElementById('file-perpanjangan').click()"
                                     class="dropzone-container">
                                    <input type="file" 
                                           name="file" 
                                           id="file-perpanjangan" 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           class="hidden" 
                                           onchange="handleFileSelect('perpanjangan', this)">

                                    <div id="dropzone-default-perpanjangan">
                                        <div class="dropzone-icon-circle circle-perpanjangan">
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
                                    <div id="file-info-perpanjangan" class="hidden selected-file-preview">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <i class="fas fa-file-circle-check text-amber-600 text-lg flex-shrink-0"></i>
                                            <div class="truncate">
                                                <p id="file-name-perpanjangan" class="text-xs font-bold text-amber-950 truncate"></p>
                                                <p id="file-size-perpanjangan" class="text-[10.5px] text-amber-700 mt-0.5"></p>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                onclick="cancelFileSelection('perpanjangan', event)" 
                                                class="w-6 h-6 rounded-full bg-amber-200/60 hover:bg-red-100 hover:text-red-600 text-amber-900 flex items-center justify-center text-xs transition" 
                                                title="Batalkan pilihan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Input Deskripsi / Catatan --}}
                            <div>
                                <label for="deskripsi-perpanjangan" class="block text-xs font-bold text-slate-700 mb-1">
                                    Catatan / Petunjuk Persyaratan (Opsional):
                                </label>
                                <textarea name="deskripsi" 
                                          id="deskripsi-perpanjangan" 
                                          rows="2" 
                                          placeholder="Tuliskan catatan perpanjangan, misal: 'Kartu PAS lama wajib dilampirkan, sertakan surat keterangan instansi...'"
                                          class="doc-textarea">{{ old('deskripsi', $dokumenPerpanjangan->deskripsi) }}</textarea>
                            </div>

                            {{-- Tombol Submit --}}
                            <div>
                                <button type="submit" 
                                        id="btn-submit-perpanjangan"
                                        class="btn-submit-main btn-submit-perpanjangan">
                                    <i class="fas fa-upload text-[#1e3a5f]"></i>
                                    <span id="btn-text-perpanjangan">{{ $dokumenPerpanjangan->hasFile() ? 'Simpan Berkas Baru' : 'Unggah Dokumen Perpanjangan' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Script Interaktif AJAX Upload, Drag & Drop, Toggle, Validation, & SweetAlert --}}
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // Toggle form ganti file jika dokumen sudah pernah diunggah
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
                icon.className = 'fas fa-sync-alt';
            }
        }

        function handleFileSelect(kategori, input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const maxBytes = 20 * 1024 * 1024; // 20 MB

            if (file.size > maxBytes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Berkas Terlalu Besar',
                    text: 'Ukuran berkas (' + formatBytes(file.size) + ') melebihi batas maksimal 20 MB.',
                    confirmButtonColor: '#1e3a5f'
                });
                input.value = '';
                return;
            }

            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(extension)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Berkas Tidak Didukung',
                    text: 'Silakan pilih berkas dengan format PDF, Word (DOC/DOCX), atau Gambar (JPG/PNG).',
                    confirmButtonColor: '#1e3a5f'
                });
                input.value = '';
                return;
            }

            // Tampilkan preview berkas yang dipilih
            document.getElementById('dropzone-default-' + kategori).classList.add('hidden');
            document.getElementById('file-info-' + kategori).classList.remove('hidden');
            document.getElementById('file-name-' + kategori).textContent = file.name;
            document.getElementById('file-size-' + kategori).textContent = 'Ukuran: ' + formatBytes(file.size);
        }

        function cancelFileSelection(kategori, event) {
            if (event) event.stopPropagation();
            const input = document.getElementById('file-' + kategori);
            input.value = '';
            document.getElementById('dropzone-default-' + kategori).classList.remove('hidden');
            document.getElementById('file-info-' + kategori).classList.add('hidden');
        }

        // Setup Drag & Drop
        ['baru', 'perpanjangan'].forEach(kategori => {
            const dropzone = document.getElementById('dropzone-' + kategori);
            const input = document.getElementById('file-' + kategori);
            if (!dropzone || !input) return;

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    input.files = files;
                    handleFileSelect(kategori, input);
                }
            }, false);
        });

        // Helper untuk render ikon file sesuai tipe
        function getFileIconHtml(doc) {
            const ext = (doc.file_type || '').toLowerCase();
            if (doc.is_pdf || ext === 'pdf') {
                return '<div class="file-icon-box icon-pdf"><i class="fas fa-file-pdf"></i></div>';
            } else if (doc.is_image || ['jpg', 'jpeg', 'png'].includes(ext)) {
                return '<div class="file-icon-box icon-img"><i class="fas fa-file-image"></i></div>';
            } else if (['doc', 'docx'].includes(ext)) {
                return '<div class="file-icon-box icon-doc"><i class="fas fa-file-word"></i></div>';
            }
            return '<div class="file-icon-box icon-doc"><i class="fas fa-file-alt"></i></div>';
        }

        // Render tampilan aktif setelah upload sukses tanpa refresh
        function renderUploadedFileDOM(kategori, doc) {
            const activeContainer = document.getElementById('active-file-container-' + kategori);
            const iconHtml = getFileIconHtml(doc);

            let noteHtml = '';
            if (doc.deskripsi) {
                noteHtml = `
                    <div class="text-[11.5px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200/80 leading-relaxed">
                        <span class="font-bold text-slate-700"><i class="fas fa-info-circle mr-1 text-blue-500"></i>Petunjuk:</span>
                        ${doc.deskripsi}
                    </div>
                `;
            }

            activeContainer.innerHTML = `
                <div class="active-file-box" id="active-box-${kategori}">
                    <div class="flex items-start gap-3.5">
                        ${iconHtml}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">
                                    ${(doc.file_type || 'FILE').toUpperCase()}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    ${doc.formatted_size || '-'}
                                </span>
                            </div>
                            <p class="text-sm font-bold text-slate-800 truncate mt-0.5" title="${doc.file_name}">
                                ${doc.file_name}
                            </p>
                            <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                                <span><i class="far fa-clock mr-1 text-slate-400"></i>${doc.updated_at || 'Baru saja'}</span>
                                <span><i class="far fa-user mr-1 text-slate-400"></i>${doc.uploader_name || 'Admin'}</span>
                            </div>
                        </div>
                    </div>
                    ${noteHtml}
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <a href="${doc.preview_url}" target="_blank" class="btn-action-primary btn-preview">
                                <i class="fas fa-eye"></i> Pratinjau
                            </a>
                            <a href="${doc.download_url}" class="btn-action-primary btn-download">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </div>
                        <button type="button" onclick="confirmDeleteAjax('${kategori}', '${doc.nama_dokumen}', ${doc.id})" class="btn-action-primary btn-delete">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </div>
                </div>
            `;

            // Update status badge
            const statusBadge = document.getElementById('status-badge-' + kategori);
            statusBadge.className = 'doc-status-badge status-uploaded';
            statusBadge.innerHTML = '<i class="fas fa-check-circle text-xs"></i> Sudah Diunggah';

            // Tampilkan tombol toggle ganti berkas
            const toggleBox = document.getElementById('btn-toggle-box-' + kategori);
            toggleBox.classList.remove('hidden');

            // Sembunyikan section upload (dapat dibuka via toggle)
            const uploadSection = document.getElementById('upload-section-' + kategori);
            uploadSection.classList.add('hidden');

            // Reset input file & state
            cancelFileSelection(kategori);

            // Update label & button text
            const label = document.getElementById('label-file-' + kategori);
            if (label) label.textContent = 'Unggah Berkas Pengganti:';
            const btnText = document.getElementById('btn-text-' + kategori);
            if (btnText) btnText.textContent = 'Simpan Berkas Baru';
        }

        // Render tampilan kosong saat berkas dihapus tanpa refresh
        function renderEmptyStateDOM(kategori, namaDokumen) {
            const activeContainer = document.getElementById('active-file-container-' + kategori);
            activeContainer.innerHTML = '';

            // Update status badge
            const statusBadge = document.getElementById('status-badge-' + kategori);
            statusBadge.className = 'doc-status-badge status-empty';
            statusBadge.innerHTML = '<i class="fas fa-clock text-xs"></i> Belum Ada Berkas';

            // Sembunyikan tombol toggle ganti berkas
            const toggleBox = document.getElementById('btn-toggle-box-' + kategori);
            toggleBox.classList.add('hidden');

            // Buka section upload
            const uploadSection = document.getElementById('upload-section-' + kategori);
            uploadSection.classList.remove('hidden');

            // Reset selection
            cancelFileSelection(kategori);

            const label = document.getElementById('label-file-' + kategori);
            if (label) label.textContent = 'Pilih Berkas Persyaratan:';
            const btnText = document.getElementById('btn-text-' + kategori);
            if (btnText) btnText.textContent = kategori === 'baru' ? 'Unggah Dokumen PAS Baru' : 'Unggah Dokumen Perpanjangan';
        }

        // UPLOAD SATU DOKUMEN VIA AJAX (TANPA REFRESH)
        function handleFormSubmitAjax(kategori, event) {
            event.preventDefault();

            const fileInput = document.getElementById('file-' + kategori);
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Berkas Terlebih Dahulu',
                    text: 'Silakan pilih atau seret berkas dokumen yang ingin diunggah.',
                    confirmButtonColor: '#1e3a5f'
                });
                return;
            }

            const btn = document.getElementById('btn-submit-' + kategori);
            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengunggah berkas...';

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', fileInput.files[0]);
            const desc = document.getElementById('deskripsi-' + kategori);
            if (desc && desc.value) {
                formData.append('deskripsi', desc.value);
            }

            fetch("{{ url('administrator/dokumen-persyaratan/upload') }}/" + kategori, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalBtnHtml;

                if (status === 200 && body.status === 'success') {
                    renderUploadedFileDOM(kategori, body.dokumen);

                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Berhasil!',
                        text: body.message,
                        timer: 2500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    let errMsg = body.message || 'Gagal mengunggah berkas.';
                    if (body.errors && body.errors.file) {
                        errMsg = body.errors.file.join(' ');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunggah',
                        text: errMsg,
                        confirmButtonColor: '#1e3a5f'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalBtnHtml;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Terjadi kesalahan saat mengunggah berkas. Silakan coba lagi.',
                    confirmButtonColor: '#1e3a5f'
                });
            });
        }

        // UPLOAD DUA DOKUMEN SEKALIGUS (BATCH UPLOAD VIA AJAX TANPA REFRESH)
        function uploadBatchAjax() {
            const fileBaru = document.getElementById('file-baru');
            const filePerp = document.getElementById('file-perpanjangan');

            const hasBaru = fileBaru.files && fileBaru.files[0];
            const hasPerp = filePerp.files && filePerp.files[0];

            if (!hasBaru && !hasPerp) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Berkas Terlebih Dahulu',
                    text: 'Pilih berkas pada formulir PAS Baru, Perpanjangan PAS, atau keduanya untuk mengunggah sekaligus.',
                    confirmButtonColor: '#1e3a5f'
                });
                return;
            }

            const batchBtn = document.getElementById('btn-batch-upload');
            const originalBatchHtml = batchBtn.innerHTML;
            batchBtn.disabled = true;
            batchBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengunggah Dokumen...';

            const formData = new FormData();
            formData.append('_token', csrfToken);

            if (hasBaru) {
                formData.append('file_baru', fileBaru.files[0]);
                const descB = document.getElementById('deskripsi-baru');
                if (descB && descB.value) formData.append('deskripsi_baru', descB.value);
            }

            if (hasPerp) {
                formData.append('file_perpanjangan', filePerp.files[0]);
                const descP = document.getElementById('deskripsi-perpanjangan');
                if (descP && descP.value) formData.append('deskripsi_perpanjangan', descP.value);
            }

            fetch("{{ route('administrator.dokumen-persyaratan.upload-batch') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                batchBtn.disabled = false;
                batchBtn.innerHTML = originalBatchHtml;

                if (status === 200 && body.status === 'success') {
                    if (body.documents.baru) {
                        renderUploadedFileDOM('baru', body.documents.baru);
                    }
                    if (body.documents.perpanjangan) {
                        renderUploadedFileDOM('perpanjangan', body.documents.perpanjangan);
                    }

                    const jumlahUploaded = (body.documents.baru ? 1 : 0) + (body.documents.perpanjangan ? 1 : 0);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diunggah!',
                        text: `${jumlahUploaded} dokumen persyaratan berhasil diperbarui tanpa reload.`,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    let errMsg = body.message || 'Gagal mengunggah berkas.';
                    if (body.errors) {
                        const allErrs = Object.values(body.errors).flat();
                        if (allErrs.length > 0) errMsg = allErrs.join(' ');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunggah',
                        text: errMsg,
                        confirmButtonColor: '#1e3a5f'
                    });
                }
            })
            .catch(err => {
                batchBtn.disabled = false;
                batchBtn.innerHTML = originalBatchHtml;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Terjadi gangguan koneksi saat mengunggah. Silakan coba kembali.',
                    confirmButtonColor: '#1e3a5f'
                });
            });
        }

        // HAPUS BERKAS VIA AJAX (TANPA REFRESH)
        function confirmDeleteAjax(kategori, namaDokumen, dokumenId) {
            Swal.fire({
                title: 'Hapus Berkas Dokumen?',
                text: "Berkas fisik untuk '" + namaDokumen + "' akan dihapus dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Berkas',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus berkas...',
                        didOpen: () => { Swal.showLoading(); },
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    fetch("{{ url('administrator/dokumen-persyaratan') }}/" + dokumenId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            renderEmptyStateDOM(kategori, namaDokumen);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berkas Dihapus',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus',
                                text: data.message || 'Terjadi kegagalan saat menghapus.',
                                confirmButtonColor: '#1e3a5f'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menghubungi server.',
                            confirmButtonColor: '#1e3a5f'
                        });
                    });
                }
            });
        }
    </script>
</x-app-layout>
