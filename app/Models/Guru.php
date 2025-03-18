<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'app_detail_guru';

    protected $fillable = [
        'username',
        'nama',
        'nip',
        'telepon',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
