<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
            'name',
            'email',
            'password',
            'role',
            'perusahaan',
    ];

    // Helper cek role (Hanya administrator dan operator)
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Perangkat kamera yang ditugaskan kepada user / operator ini.
     */
    public function cameraDevices()
    {
        return $this->belongsToMany(CameraDevice::class, 'camera_device_user', 'user_id', 'camera_device_id')
            ->withTimestamps();
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

