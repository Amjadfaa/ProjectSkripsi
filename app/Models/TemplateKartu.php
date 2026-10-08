<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateKartu extends Model
{
    use HasFactory;

    protected $table = 'template_kartus';

    protected $fillable = [
        'nama_template',
        'kode_warna',
        'warna_label',
        'warna_hex',
        'warna_teks',
        'gambar_template',
        'area_akses',
        'prioritas',
        'is_default',
        'is_active',
        'keterangan',
        'posisi_pengaturan',
    ];

    protected $casts = [
        'area_akses'        => 'array',
        'posisi_pengaturan' => 'array',
        'is_default'        => 'boolean',
        'is_active'         => 'boolean',
        'prioritas'         => 'integer',
    ];

    /**
     * URL publik untuk gambar template kartu
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (!$this->gambar_template) {
            return null;
        }

        return asset('storage/' . $this->gambar_template);
    }

    /**
     * Pengaturan posisi elemen default (dalam persen %)
     */
    public static function getDefaultPosisiPengaturan(string $kodeWarna = 'biru'): array
    {
        $textColor = ($kodeWarna === 'kuning') ? '#000000' : '#FFFFFF';

        return [
            'foto' => [
                'top'       => 28.0,
                'left'      => 49.5,
                'width'     => 43.5,
                'height'    => 30.5,
                'visible'   => true,
            ],
            'area_akses' => [
                'top'       => 27.0,
                'left'      => 12.0,
                'width'     => 28.0,
                'font_size' => 20,
                'color'     => $textColor,
                'align'     => 'center',
                'direction' => 'vertical',
                'visible'   => true,
            ],
            'masa_berlaku' => [
                'top'       => 22.5,
                'left'      => 49.5,
                'width'     => 43.5,
                'font_size' => 11,
                'color'     => $textColor,
                'align'     => 'center',
                'bold'      => true,
                'visible'   => true,
            ],
            'nama_pemegang' => [
                'top'       => 67.5,
                'left'      => 7.0,
                'width'     => 58.0,
                'font_size' => 11,
                'color'     => $textColor,
                'align'     => 'left',
                'bold'      => true,
                'uppercase' => true,
                'visible'   => true,
            ],
            'jabatan' => [
                'top'       => 72.5,
                'left'      => 7.0,
                'width'     => 58.0,
                'font_size' => 9,
                'color'     => $textColor,
                'align'     => 'left',
                'bold'      => false,
                'uppercase' => true,
                'visible'   => true,
            ],
            'instansi' => [
                'top'       => 77.0,
                'left'      => 7.0,
                'width'     => 58.0,
                'font_size' => 9,
                'color'     => $textColor,
                'align'     => 'left',
                'bold'      => false,
                'uppercase' => true,
                'visible'   => true,
            ],
            'no_registrasi' => [
                'top'       => 81.5,
                'left'      => 7.0,
                'width'     => 58.0,
                'font_size' => 8.5,
                'color'     => $textColor,
                'align'     => 'left',
                'bold'      => false,
                'uppercase' => true,
                'visible'   => true,
            ],
            'qr_code' => [
                'top'       => 68.0,
                'left'      => 68.0,
                'width'     => 24.0,
                'height'    => 24.0,
                'visible'   => true,
            ],
        ];
    }

    /**
     * Dapatkan konfigurasi posisi gabungan antara database dan default
     */
    public function getPosisiAttribute(): array
    {
        $default = self::getDefaultPosisiPengaturan($this->kode_warna ?? 'biru');
        $custom = is_array($this->posisi_pengaturan) ? $this->posisi_pengaturan : [];

        // Gabungkan array secara mendalam
        $merged = $default;
        foreach ($custom as $key => $values) {
            if (isset($merged[$key]) && is_array($values)) {
                $merged[$key] = array_merge($merged[$key], $values);
            } else {
                $merged[$key] = $values;
            }
        }

        return $merged;
    }

    /**
     * Cek apakah template ini memiliki kode area tertentu
     */
    public function hasArea(string $kodeArea): bool
    {
        $areas = is_array($this->area_akses) ? $this->area_akses : [];
        return in_array(strtoupper(trim($kodeArea)), array_map('strtoupper', $areas));
    }

    /**
     * Resolusi template kartu terbaik untuk data Kartu PAS atau daftar area
     * Diurutkan berdasarkan kecocokan zonasi area akses
     *
     * @param KartuPas|array|string $kartuOrAreas
     * @return TemplateKartu|null
     */
    public static function resolveTemplateForKartu($kartuOrAreas): ?self
    {
        $targetAreas = [];

        if ($kartuOrAreas instanceof KartuPas) {
            $raw = KartuPas::normalizeAreaAkses($kartuOrAreas->area_akses ?? '');
            $targetAreas = array_filter(array_map('trim', explode(',', $raw)));
        } elseif (is_string($kartuOrAreas)) {
            $raw = KartuPas::normalizeAreaAkses($kartuOrAreas);
            $targetAreas = array_filter(array_map('trim', explode(',', $raw)));
        } elseif (is_array($kartuOrAreas)) {
            $raw = KartuPas::normalizeAreaAkses($kartuOrAreas);
            $targetAreas = array_filter(array_map('trim', explode(',', $raw)));
        }

        $targetAreas = array_map('strtoupper', $targetAreas);

        // Ambil semua template aktif yang diurutkan berdasarkan ID
        $activeTemplates = self::where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        // 1. Cek kecocokan area akses
        if (!empty($targetAreas)) {
            foreach ($activeTemplates as $tpl) {
                $tplAreas = is_array($tpl->area_akses) ? array_map('strtoupper', $tpl->area_akses) : [];
                $intersection = array_intersect($targetAreas, $tplAreas);
                if (!empty($intersection)) {
                    return $tpl;
                }
            }
        }

        // 2. Fallback ke template aktif pertama jika area tidak cocok dengan zonasi apapun
        return $activeTemplates->first();
    }

    /**
     * Resolusi template kartu untuk satu kode area tertentu (misal area kamera operator)
     */
    public static function resolveTemplateForArea(string $kodeArea): ?self
    {
        $cleanArea = strtoupper(trim($kodeArea));

        $activeTemplates = self::where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($activeTemplates as $tpl) {
            $tplAreas = is_array($tpl->area_akses) ? array_map('strtoupper', $tpl->area_akses) : [];
            if (in_array($cleanArea, $tplAreas)) {
                return $tpl;
            }
        }

        return $activeTemplates->firstWhere('is_default', true) ?? $activeTemplates->first();
    }
}
