<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumenPersyaratan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_persyaratans';

    protected $fillable = [
        'kategori',
        'nama_dokumen',
        'deskripsi',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'uploaded_by',
    ];

    /**
     * Relasi ke user yang mengunggah dokumen
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Mengecek apakah dokumen memiliki file fisik yang valid
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Format ukuran file menjadi string yang mudah dibaca (KB / MB)
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * URL publik untuk file
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        return asset('storage/' . $this->file_path);
    }

    /**
     * Cek apakah tipe file berupa PDF
     */
    public function isPdf(): bool
    {
        return strtolower($this->file_type ?? '') === 'pdf' ||
               str_ends_with(strtolower($this->file_name ?? ''), '.pdf');
    }

    /**
     * Cek apakah tipe file berupa gambar
     */
    public function isImage(): bool
    {
        $ext = strtolower(pathinfo($this->file_name ?? '', PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    }
}
