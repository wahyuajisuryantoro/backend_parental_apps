<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'app_login';

    protected $fillable = [
        'username',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function detailGuru()
    {
        return $this->hasOne(Guru::class, 'username', 'username')
                    ->where('role', 'guru');
    }

    public function detailOrangTua()
    {
        return $this->hasOne(OrangTua::class, 'username', 'username');
    }

    public function getProfileData()
    {
        return $this->role === 'guru' ? $this->detailGuru : $this->detailOrangTua;
    }

    public function anak(): HasMany
    {
        return $this->hasMany(Siswa::class, 'no_kk', 'no_kk');
    }
}
