<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_instansi',
        'kuota',
        'alamat',
        'telepon',
        'email',
        'is_active',
    ];
    
    public function kartuPas()
    {
        return $this->hasMany(\App\Models\KartuPas::class, 'instansi_id');
    }

    public function getSisaKuotaAttribute(): int
    {
        $aktif = $this->kartuPas()->where('status', 'aktif')->count();
        if ($this->id) {
            $aktifByName = KartuPas::whereNull('instansi_id')
                ->where('perusahaan', $this->nama_instansi)
                ->where('status', 'aktif')
                ->count();
            $aktif += $aktifByName;
        }
        return max(0, $this->kuota - $aktif);
    }
}

