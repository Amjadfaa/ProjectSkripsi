<div class="mb-4">
    <label class="block font-medium text-gray-700">Instansi / Perusahaan</label>
    <select name="perusahaan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">-- Pilih Instansi --</option>
        @foreach($instansiList as $instansi)
            <option value="{{ $instansi->nama_instansi }}" {{ old('perusahaan', $permohonan->perusahaan) == $instansi->nama_instansi ? 'selected' : '' }}>
                {{ $instansi->nama_instansi }}
            </option>
        @endforeach
    </select>
    @error('perusahaan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
</div>
